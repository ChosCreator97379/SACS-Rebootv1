<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso | SACS Nuevo</title>
    <style>
        :root {
            --ink: #17212b;
            --muted: #697586;
            --paper: #f5f7f4;
            --panel: #ffffff;
            --navy: #142b3a;
            --navy-soft: #20465a;
            --teal: #1d7a78;
            --gold: #d69e47;
            --line: #dce4e1;
            --shadow: 0 18px 45px rgba(20, 43, 58, .08);
            --error: #c53030;
            --error-bg: #fff5f5;
        }

        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--ink); 
            font-family: "Trebuchet MS", sans-serif; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 80%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 50%;
            height: 60%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .login-container { width: 100%; max-width: 420px; padding: 20px; }
        
        .login-card { padding: 40px; background: var(--panel); border: 1px solid var(--line); border-radius: 12px; box-shadow: var(--shadow); }
        
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .brand-mark { display: grid; place-items: center; width: 48px; height: 48px; border: 2px solid var(--gold); border-radius: 50%; color: var(--gold); font-family: Georgia, serif; font-size: 24px; }
        .brand-name { font-family: Georgia, serif; font-size: 28px; letter-spacing: .02em; }
        .brand-note { display: block; margin-top: 4px; color: var(--muted); font-size: 12px; letter-spacing: .12em; text-transform: uppercase; }
        
        .intro { margin: 24px 0 32px; color: var(--muted); font-size: 14px; line-height: 1.6; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: var(--ink); font-size: 13px; font-weight: 600; letter-spacing: .02em; }
        
        .password-row { display: flex; gap: 10px; align-items: stretch; }
        .password-row input { flex: 1; }
        .password-toggle {
            width: 44px;
            padding: 0;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 6px;
            cursor: pointer;
            color: var(--muted);
            font-size: 16px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover { 
            background: var(--paper); 
            color: var(--teal); 
            border-color: var(--teal);
        }
        
        input { 
            width: 100%; 
            padding: 12px 14px; 
            border: 1px solid var(--line); 
            border-radius: 6px; 
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        input:focus { 
            outline: none; 
            border-color: var(--teal); 
            box-shadow: 0 0 0 3px rgba(29, 122, 120, 0.1); 
        }
        
        button { 
            width: 100%; 
            padding: 14px; 
            border: 0; 
            border-radius: 6px; 
            background: var(--teal); 
            color: #fff; 
            font-size: 14px; 
            font-weight: 600; 
            cursor: pointer; 
            font-family: inherit;
            letter-spacing: .04em;
            transition: background 0.2s, transform 0.1s;
        }
        
        button:hover { background: #166261; }
        button:active { transform: translateY(1px); }
        
        .error { 
            padding: 14px; 
            background: var(--error-bg); 
            border-left: 4px solid var(--error); 
            border-radius: 4px; 
            color: var(--error); 
            font-size: 13px; 
            margin-bottom: 24px;
        }
        
        .footer { 
            margin-top: 24px; 
            padding-top: 20px; 
            border-top: 1px solid var(--line); 
            text-align: center; 
            color: var(--muted); 
            font-size: 12px; 
        }
        
        @media (max-width: 480px) {
            .login-container { padding: 16px; }
            .login-card { padding: 32px 24px; }
            .brand-name { font-size: 24px; }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="brand">
            <span class="brand-mark">S</span>
            <span>
                <span class="brand-name">SACS</span>
                <span class="brand-note">gestion social</span>
            </span>
        </div>
        
        <p class="intro">Ingrese sus credenciales para acceder al sistema de administración social.</p>
        
        <?php if ($error !== null): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        
        <form method="post" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="username">Usuario</label>
                <input id="username" name="username" maxlength="200" required autocomplete="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" placeholder="Ingrese su usuario">
            </div>
            
            <div class="form-group">
                <label for="password">Clave</label>
                <div class="password-row">
                    <input id="password" name="password" type="password" maxlength="60" required autocomplete="current-password" placeholder="Ingrese su clave">
                    <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Mostrar contraseña">👁</button>
                </div>
            </div>
            
            <button type="submit">Ingresar al sistema</button>
        </form>
        
        <div class="footer">
            Sistema de Administración Social - Base de pruebas
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.textContent = '🙈';
        toggleButton.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
        passwordInput.type = 'password';
        toggleButton.textContent = '👁';
        toggleButton.setAttribute('aria-label', 'Mostrar contraseña');
    }
}
</script>
</body>
</html>
