<?php

declare(strict_types=1);

namespace Sacs\Repositories;

use PDO;
use Sacs\Core\Database;

final class CajaRepository
{
    public function listMovements(array $filters): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = min(100, max(10, (int) ($filters['per_page'] ?? 25)));

        // Permitir búsqueda si hay código de movimiento o si hay cliente
        if (trim((string) ($filters['client'] ?? '')) === '' && trim((string) ($filters['movement_code'] ?? '')) === '') {
            return [
                'items' => [],
                'page' => $page,
                'per_page' => $perPage,
                'total' => 0,
                'pages' => 1,
                'searched' => false,
            ];
        }

        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $parameters = [];

        if (($filters['date_from'] ?? '') !== '') {
            $conditions[] = 'm.f15_fecha >= :date_from';
            $parameters['date_from'] = $filters['date_from'];
        }
        if (($filters['date_to'] ?? '') !== '') {
            $conditions[] = 'm.f15_fecha <= :date_to';
            $parameters['date_to'] = $filters['date_to'];
        }
        if (($filters['operation'] ?? '') !== '') {
            $conditions[] = 'm.f15_operacion = :operation';
            $parameters['operation'] = (int) $filters['operation'];
        }
        if (($filters['client'] ?? '') !== '') {
            $conditions[] = 'm.f15_cliente = :client';
            $parameters['client'] = trim($filters['client']);
        }
        if (($filters['movement_code'] ?? '') !== '') {
            $conditions[] = 'm.f15_codigo = :movement_code';
            $parameters['movement_code'] = trim($filters['movement_code']);
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $database = Database::connection();

        $countStatement = $database->prepare("SELECT COUNT(*) FROM f15_movimiento m {$where}");
        $countStatement->execute($parameters);
        $total = (int) $countStatement->fetchColumn();

        $query = <<<'SQL'
            SELECT
                m.f15_codigo AS id,
                m.f15_fecha AS operation_date,
                m.f15_operacion AS operation_id,
                o.f14_nombre AS operation_name,
                m.f15_cliente AS client_id,
                TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS client_name,
                m.f15_maportacion AS contribution_amount,
                m.f15_mretiro AS withdrawal_amount,
                m.f15_maprestamo AS loan_payment_amount,
                m.f15_mdesembolso AS disbursement_amount,
                m.f15_mbeneficio AS benefit_amount,
                m.f15_mamulta AS fine_amount,
                m.f15_marifa AS raffle_amount,
                m.f15_devolucion AS refund_amount,
                m.f15_megreso AS expense_amount,
                m.f15_ingreso AS income_amount,
                m.f15_gastosa AS admin_expense,
                m.f15_gastosm AS late_fee,
                m.f15_gastos1 AS other_one,
                m.f15_gastos2 AS other_two,
                m.f15_formaPago AS payment_method,
                m.f15_usuaCrea AS created_by,
                m.f15_obs AS observation
            FROM f15_movimiento m
            INNER JOIN f14_operacion o ON o.f14_codigo = m.f15_operacion
            LEFT JOIN f02_cliente c ON CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(m.f15_cliente AS UNSIGNED)
            LEFT JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
        SQL;

        $statement = $database->prepare("{$query} {$where} ORDER BY m.f15_codigo DESC LIMIT :limit OFFSET :offset");
        foreach ($parameters as $name => $value) {
            $statement->bindValue(':' . $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'searched' => true,
        ];
    }

    public function operations(): array
    {
        return Database::connection()
            ->query('SELECT f14_codigo AS id, f14_nombre AS name FROM f14_operacion ORDER BY f14_codigo')
            ->fetchAll();
    }

    public function accountingCodes(): array
    {
        return Database::connection()
            ->query('SELECT codigo AS code, nombre_cuenta AS name FROM f38_codigo_contable ORDER BY codigo')
            ->fetchAll();
    }

    public function findMovement(int $id): ?array
    {
        $query = <<<'SQL'
            SELECT
                m.f15_codigo AS id,
                m.f15_fecha AS operation_date,
                m.f15_operacion AS operation_id,
                o.f14_nombre AS operation_name,
                m.f15_cliente AS client_id,
                TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS client_name,
                m.f15_maportacion AS contribution_amount,
                m.f15_mretiro AS withdrawal_amount,
                m.f15_maprestamo AS loan_payment_amount,
                m.f15_mdesembolso AS disbursement_amount,
                m.f15_mbeneficio AS benefit_amount,
                m.f15_mamulta AS fine_amount,
                m.f15_marifa AS raffle_amount,
                m.f15_devolucion AS refund_amount,
                m.f15_megreso AS expense_amount,
                m.f15_ingreso AS income_amount,
                m.f15_gastosa AS admin_expense,
                m.f15_gastosm AS late_fee,
                m.f15_gastos1 AS other_one,
                m.f15_gastos2 AS other_two,
                m.f15_formaPago AS payment_method,
                m.f15_usuaCrea AS created_by,
                m.f15_fechaCrea AS created_at,
                m.f15_usuaModi AS updated_by,
                m.f15_fechaModi AS updated_at,
                m.f15_prestamo AS loan_id,
                m.f15_cheque AS check_number,
                m.f15_serie AS series,
                m.f15_obs AS observation,
                m.f15_obs2 AS observation_two,
                m.f15_std AS installment,
                pmt.f16_estado AS loan_status,
                d.f37_codigo AS accounting_id,
                d.f37_estado AS accounting_status
            FROM f15_movimiento m
            INNER JOIN f14_operacion o ON o.f14_codigo = m.f15_operacion
            LEFT JOIN f02_cliente c ON CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(m.f15_cliente AS UNSIGNED)
            LEFT JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
            LEFT JOIN f16_prestamo pmt ON pmt.f16_codigo = m.f15_prestamo
            LEFT JOIN f37_diario_contable d ON d.f37_movimiento = m.f15_codigo
            WHERE m.f15_codigo = :id
            LIMIT 1
        SQL;

        $statement = Database::connection()->prepare($query);
        $statement->execute(['id' => $id]);
        $movement = $statement->fetch();

        return $movement === false ? null : $movement;
    }

    public function findClientProfile(string $clientId): ?array
    {
        $query = <<<'SQL'
            SELECT
                CAST(c.F02_ID_CLIENTE AS UNSIGNED) AS client_id,
                TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS full_name,
                p.F01_NUM_DOC AS document,
                p.F01_CENTRO AS center,
                p.F01_CONDICION AS `condition`
            FROM f02_cliente c
            INNER JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
            WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:client AS UNSIGNED)
            LIMIT 1
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);
        $client = $statement->fetch();

        return $client === false ? null : $client;
    }

    public function clientLoans(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f16_codigo AS id, f16_estado AS status, f16_producto AS product,
                   f16_importe AS amount, f16_saldo AS aporte, f16_saldo AS balance, f16_cuotasPend AS pending_installments,
                   f16_sobre AS sobregiro
            FROM f16_prestamo
            WHERE f16_cliente = :client
            ORDER BY f16_codigo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientLoansWithOverdraft(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f16_codigo AS id, f16_estado AS status, f16_producto AS product,
                   f16_importe AS amount, f16_saldo AS balance, f16_sobre AS sobregiro,
                   f16_gastosa AS gastos_adm, f16_gastosm AS mora
            FROM f16_prestamo
            WHERE f16_cliente = :client AND f16_estado = '4' AND f16_sobre > 0
            ORDER BY f16_codigo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientLoansPendingDisbursement(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f16_codigo AS id, f16_estado AS status, f16_producto AS product,
                   f16_importe AS amount, f16_saldo AS balance, f16_gastosa AS gastos_adm
            FROM f16_prestamo
            WHERE f16_cliente = :client AND f16_estado = 3
            ORDER BY f16_codigo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function accountingEntries(array $filters = []): array
    {
        $query = <<<'SQL'
            SELECT d.f37_codigo AS id, d.f37_movimiento AS movement_id, d.f37_fecha AS date,
                   d.f37_codigo_contable AS accounting_code, d.f37_operacion AS operation,
                   d.f37_descripcion AS description, d.f37_debe AS debe, d.f37_haber AS haber,
                   d.f37_usuario_crea AS user, d.f37_fecha_crea AS created_at, d.f37_estado AS status,
                   o.f14_nombre AS operation_name, m.f15_cliente AS client_id
            FROM f37_diario_contable d
            LEFT JOIN f14_operacion o ON o.f14_codigo = d.f37_operacion
            LEFT JOIN f15_movimiento m ON m.f15_codigo = d.f37_movimiento
            WHERE 1=1
        SQL;

        $params = [];

        if (!empty($filters['date_from'])) {
            $query .= " AND d.f37_fecha >= :date_from";
            $params['date_from'] = trim($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND d.f37_fecha <= :date_to";
            $params['date_to'] = trim($filters['date_to']);
        }

        if (!empty($filters['operation'])) {
            $query .= " AND d.f37_operacion = :operation";
            $params['operation'] = (int) trim($filters['operation']);
        }

        if (!empty($filters['accounting_code'])) {
            $query .= " AND d.f37_codigo_contable = :accounting_code";
            $params['accounting_code'] = trim($filters['accounting_code']);
        }

        if (!empty($filters['client'])) {
            $query .= " AND m.f15_cliente = :client";
            $params['client'] = trim($filters['client']);
        }

        $query .= " ORDER BY d.f37_fecha DESC, d.f37_codigo DESC";

        if (!empty($filters['per_page'])) {
            $offset = ((int) ($filters['page'] ?? 1) - 1) * (int) $filters['per_page'];
            $query .= " LIMIT " . (int) $filters['per_page'] . " OFFSET " . $offset;
        } else {
            $query .= " LIMIT 50";
        }

        $statement = Database::connection()->prepare($query);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function accountingEntryTotal(array $filters = []): int
    {
        $query = <<<'SQL'
            SELECT COUNT(*) AS total
            FROM f37_diario_contable d
            LEFT JOIN f15_movimiento m ON m.f15_codigo = d.f37_movimiento
            WHERE 1=1
        SQL;

        $params = [];

        if (!empty($filters['date_from'])) {
            $query .= " AND d.f37_fecha >= :date_from";
            $params['date_from'] = trim($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND d.f37_fecha <= :date_to";
            $params['date_to'] = trim($filters['date_to']);
        }

        if (!empty($filters['operation'])) {
            $query .= " AND d.f37_operacion = :operation";
            $params['operation'] = (int) trim($filters['operation']);
        }

        if (!empty($filters['accounting_code'])) {
            $query .= " AND d.f37_codigo_contable = :accounting_code";
            $params['accounting_code'] = trim($filters['accounting_code']);
        }

        if (!empty($filters['client'])) {
            $query .= " AND m.f15_cliente = :client";
            $params['client'] = trim($filters['client']);
        }

        $statement = Database::connection()->prepare($query);
        $statement->execute($params);
        $result = $statement->fetch();

        return (int) ($result['total'] ?? 0);
    }

    public function clientInstallments(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT q.f17_prestamo AS loan_id, q.f17_ncuota AS number,
                   q.f17_fecha AS date, q.f17_total AS total
            FROM f17_cuotasprestamo q
            INNER JOIN f16_prestamo p ON p.f16_codigo = q.f17_prestamo
            WHERE p.f16_cliente = :client
              AND q.f17_estado = 'VIGENTE'
            ORDER BY q.f17_fecha, q.f17_prestamo, q.f17_ncuota
            LIMIT 50
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientContributions(string $clientId, string $table, string $additionalFilter = ''): array
    {
        $allowedTables = ['f22_aportes', 'f27_aportes_cope'];
        if (!in_array($table, $allowedTables, true)) {
            throw new \InvalidArgumentException('Tabla de aportaciones no permitida.');
        }

        $isCope = $table === 'f27_aportes_cope';
        $period = $isCope ? 'f27_periodo' : 'f22_periodo';
        $planned = $isCope ? 'f27_aportacionP' : 'f22_aportacionP';
        $paid = $isCope ? 'f27_aportacionD' : 'f22_aportacionD';
        $paidDate = $isCope ? 'f27_fechaPago' : 'f22_fechaPago';
        $query = "SELECT {$period} AS period, {$planned} AS planned, {$paid} AS paid, {$paidDate} AS paid_date
                  FROM {$table}
                  WHERE " . ($isCope ? 'f27_socio' : 'f22_socio') . " = :client
                  {$additionalFilter}
                  ORDER BY {$period} DESC
                  LIMIT 12";
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientFines(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f63_codigo AS id, f63_periodo AS period,
                   f63_multap AS planned, f63_multad AS paid,
                   f63_multar AS remaining, f63_fechaPago AS paid_date,
                   f63_formaPago AS payment_method
            FROM f63_multa
            WHERE CAST(f63_socio AS UNSIGNED) = CAST(:client AS UNSIGNED)
            ORDER BY f63_periodo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientPendingFines(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f63_codigo AS id, f63_periodo AS period,
                   f63_multap AS planned, f63_multad AS paid,
                   f63_multar AS remaining, f63_fechaPago AS paid_date,
                   f63_formaPago AS payment_method
            FROM f63_multa
            WHERE CAST(f63_socio AS UNSIGNED) = CAST(:client AS UNSIGNED)
              AND f63_multar > 0
            ORDER BY f63_periodo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientRaffles(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f64_codigo AS id, f64_periodo AS period,
                   f64_rifap AS planned, f64_rifad AS paid,
                   f64_rifar AS remaining, f64_fechaPago AS paid_date,
                   f64_formaPago AS payment_method
            FROM f64_rifa
            WHERE CAST(f64_socio AS UNSIGNED) = CAST(:client AS UNSIGNED)
            ORDER BY f64_periodo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => trim($clientId)]);

        return $statement->fetchAll();
    }

    public function clientBenefits(string $clientId): array
    {
        $query = <<<'SQL'
            SELECT f21_codigo AS id, f21_fecha AS date,
                   f21_importe AS amount, f21_tipo AS type,
                   f21_estado AS status, f21_beneficiario AS beneficiary,
                   f21_tramite AS procedure_code
            FROM f21_beneficios
            WHERE f21_socio = :client
            ORDER BY f21_codigo DESC
            LIMIT 20
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute(['client' => (int) $clientId]);

        return $statement->fetchAll();
    }

    public function simulateInstallmentPayment(string $clientId, int $loanId, int $installmentNumber): ?array
    {
        $query = <<<'SQL'
            SELECT
                p.f16_codigo AS loan_id,
                p.f16_cliente AS client_id,
                p.f16_estado AS loan_status,
                p.f16_saldo AS loan_balance,
                p.f16_cuotasPend AS pending_installments,
                p.f16_gastosa AS loan_admin_expense,
                p.f16_gastosm AS loan_late_fee,
                p.f16_otros AS loan_other_one,
                p.f16_otros2 AS loan_other_two,
                q.f17_ncuota AS installment_number,
                q.f17_fecha AS installment_date,
                q.f17_capital AS capital,
                q.f17_interes AS interest,
                q.f17_total AS installment_total,
                q.f17_sobregiro AS overdraft,
                q.f17_interesSG AS overdraft_interest,
                q.f17_mora AS installment_late_fee,
                q.f17_moraSG AS overdraft_late_fee
            FROM f16_prestamo p
            INNER JOIN f17_cuotasprestamo q ON q.f17_prestamo = p.f16_codigo
            WHERE p.f16_codigo = :loan
              AND CAST(p.f16_cliente AS UNSIGNED) = CAST(:client AS UNSIGNED)
              AND q.f17_ncuota = :installment
                            AND q.f17_ncuota > 0
              AND q.f17_estado = 'VIGENTE'
            LIMIT 1
        SQL;
        $statement = Database::connection()->prepare($query);
        $statement->execute([
            'loan' => $loanId,
            'client' => trim($clientId),
            'installment' => $installmentNumber,
        ]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $capital = $this->decimal($row['capital']);
        $interest = $this->decimal($row['interest']);
        $installmentLateFee = $this->decimal($row['installment_late_fee']);
        $overdraft = $this->decimal($row['overdraft']);
        $overdraftInterest = $this->decimal($row['overdraft_interest']);
        $overdraftLateFee = $this->decimal($row['overdraft_late_fee']);
        $total = $capital + $interest + $installmentLateFee + $overdraft + $overdraftInterest + $overdraftLateFee;
        $balance = $this->decimal($row['loan_balance']);

        return [
            'loan_id' => (int) $row['loan_id'],
            'client_id' => (string) $row['client_id'],
            'loan_status' => (int) $row['loan_status'],
            'loan_balance' => number_format($balance, 2, '.', ''),
            'pending_installments' => (int) $row['pending_installments'],
            'installment_number' => (int) $row['installment_number'],
            'installment_date' => $row['installment_date'],
            'capital' => number_format($capital, 2, '.', ''),
            'interest' => number_format($interest, 2, '.', ''),
            'installment_late_fee' => number_format($installmentLateFee, 2, '.', ''),
            'overdraft' => number_format($overdraft, 2, '.', ''),
            'overdraft_interest' => number_format($overdraftInterest, 2, '.', ''),
            'overdraft_late_fee' => number_format($overdraftLateFee, 2, '.', ''),
            'simulated_total' => number_format($total, 2, '.', ''),
            'simulated_balance' => number_format(max(0, $balance - $capital), 2, '.', ''),
            'writes' => ['f17_cuotasprestamo', 'f16_prestamo', 'f15_movimiento', 'f37_diario_contable'],
        ];
    }

    private function decimal(mixed $value): float
    {
        return $value === null || $value === '' ? 0.0 : (float) $value;
    }
}
