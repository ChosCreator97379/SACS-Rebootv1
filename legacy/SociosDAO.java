package com.pe.sacs.logica.socios;

import com.pe.sacs.logica.entidades.AportacionBean;
import com.pe.sacs.logica.entidades.BeneficiosBean;
import com.pe.sacs.logica.entidades.ChequeBean;
import com.pe.sacs.logica.entidades.ClienteBean;
import com.pe.sacs.logica.entidades.CronogramaBean;
import com.pe.sacs.logica.entidades.FamiliarBean;
import com.pe.sacs.logica.entidades.MultaBean;
import com.pe.sacs.logica.entidades.RifaBean;
import com.pe.sacs.logica.entidades.SocioBean;

import com.pe.sacs.logica.entidades.TarifaAnualBean;

import java.util.List;

public interface SociosDAO {
    public RifaBean getRifaPendiente(Long codigo);

       public MultaBean getMultaPendiente(Long codigo);
       public boolean actualizaRifaCaja(RifaBean rifa);
       public boolean actualizaMultaCaja(MultaBean multa);
    public boolean actualizaMulta(MultaBean multa);
       public boolean actualizaRifa(RifaBean rifa);
       public MultaBean getMulta(Long codigo);
       public RifaBean getRifa(Long codigo);
    public boolean eliminarMulta(Long multa);
      public boolean eliminarRifa(Long rifa) ;
      public int registrarRifa(RifaBean aportacion) ;
      public int registrarMulta(MultaBean aportacion);
      public List listarMultas(String codigo, String fechaDesde, 
                                     String fechaHasta);
                                     public List listarRifas(String codigo, String fechaDesde, 
                                                                    String fechaHasta);   
    public String getFecUltimoFonbes(Long codigo);
    public boolean eliminarSocioParcial(Integer codigo, String fec) ;
    public boolean eliminarSocioFalle(Integer codigo) ;
    public List listarSociosActivos(String tipo, String criterio);
    public boolean tieneCompraDeuda(Integer codigo) ;
    public SocioBean getSocioporClienteFam(String codigo);
    public double getSobgregiroCoopamTotal(long cliente);
    public double getSobgregiroFonbesTotal(long cliente) ;
    public boolean actualizaAportacionFonbes(AportacionBean aportacion);
    public boolean actualizaAportacionCoopam(AportacionBean aportacion);
    public List listaSobregiroFonbesSocio(String codigo);
    public List listaSobregiroCoopamSocio(String codigo);
    public List listarDescuentosProducto3(String periodo, String ano, 
                                         String condicion, String centro, 
                                         String producto);
                                         public AportacionBean getDescuento(int socio, String periodo);
                                         public List listarDescuentosProducto0G(String periodo, String ano, 
                                                                               String tipo, String centro, 
                                                                               String producto);
    public double getTotalDevoFonbes(int cliente);
    public boolean ocultarSocio(Integer codigo, int visible);
    public double getTotalDevoCoopam(int cliente);
    public List listarSociosExportarProducto2(String centro, String c, 
                                                 String mes, String ano, 
                                                 String producto);
    public int registrarSocioAuditoria2(SocioBean socio);
    public List listarSocioAuditoria(String desde,String hasta,  String usuario,String socio,String tipo);
    public List listarFamiliaresT11(String codigo);
    public boolean actualizaParentesco(String persona, String socio);
    public double totalAporta(String socio ) ;
    public double totalAportaCope(String socio ) ;
    public boolean actualizaAportacionCope(AportacionBean aportacion);
    public AportacionBean getAportaCope(int socio, String periodo);
    public boolean eliminarApoCope(AportacionBean aportacion);
    public List listarAportacionesCope(String codigo, String fechaDesde, 
                                   String fechaHasta, String estado) ;
    public List listarFamiliares9(String codigo,String persona);
    public List listarCheques(String fechaDesde, String fechaHasta );
    public List listarDescuentosD(String periodo,String tipo,String centro);
    public int actualizarDescuento(AportacionBean aportacion);
    public List listarBeneficios2();
    public List listarAportacionesY(String codigo, String fechaDesde, 
                                   String fechaHasta, String estado);
    public List listarDescuentos3(String periodo,String ano,String tipo,String centro ) ;
    public List listarAportaciones(String codigo, String fechaDesde, String fechaHasta,String estado );
    public List listarDescuentos(String periodo,String ano,String condicion,String centr,String producto);
    public boolean actualizarBeneficio(BeneficiosBean socio);
    public BeneficiosBean getBeneficio(Integer codigo);
    public List listarSocios(int estado) ;
    public String getAportacionTotalCo(Integer codigo);
    public List listarFamiliaresBaja(String codigo);
    public List listarBeneficiosBaja(String socio,String desde,String hasta);
    public SocioBean getSocioporClienteBaja(String codigo) ;
    public List listarSociosCandi(String centro,String condicion) ;;
    public List listarSociosR2 (String departamento,String provincia ,String distrito ,String sexo,String edad ,String estado,String condicion,String centro,String situacion,String mr,String ar, String mi,String ai ,String fecha);
    public List listarSociosAtrazados(String centro,String condicion);
    public SocioBean getSocio2(Long codigo);
    public boolean actualizarCarta(SocioBean socio);
    public boolean registrarObs(SocioBean socio);
    public List listarObs(String socio);
    public boolean actualizarObs(SocioBean socio);
    public boolean registrarDescuentoTemp(AportacionBean aportacion);
    public List listarDescuentosTemp(String periodo,String ano,String tipo,String centro,String producto );
    public int validaAportes(String socio,String periodo);
    public boolean actualizaAportacion0(AportacionBean aportacion);
    public boolean actualizaDescuento0(AportacionBean aportacion);
    public boolean actualizaDescuento1(AportacionBean aportacion);
    public List listarBeneficiosT(String socio);
    public List listarFamiliaresT(String codigo);
    public List listarSociosPronostico(String centro,String c,String horizonte);
    public List listarFamiliaresR(String departamento,String provincia ,String distrito ,String sexo,String edad ,String estado,String condicion,String centro,String situacion,String mr,String ar, String mi,String ai,String mn,String an,String pa ) ;
    public boolean activarSocio(Integer codigo);
    public List listarDescuentosReporte(String socio,String mes,String ano);
    public int registrarSocio(SocioBean socio);
    public boolean actualizaDescuento(AportacionBean aportacion);
    public List listarInstruccion() ;
    public List listarAportacionesSocio(String codigo  ) ;
    public boolean actualizaAportacion(AportacionBean aportacion);
    public List listarProfesion() ;
    public List listarSociosFEBAN() ;
    public boolean eliminarDescuento(AportacionBean aportacion) ;
    public boolean eliminarSocioFinal(Integer codigo);
    public int registrarAportacionD(AportacionBean aportacion);
    public boolean eliminarSocio(Integer codigo) ;
    public List listarNacionalidad() ;
    public  List listarDescuentos2(String periodo,String ano,String tipo,String centro ) ;
    public List listarSociosExportar(String centro,String c,String mes,String ano );
    public boolean registrarDescuento(AportacionBean aportacion);
   // public int registrarAportacion(AportacionBean aportacion);
    public List listarSocios(String tipo,String criterio) ;
    public List getDistritos(String depa,String prov);
    public List getProvincias(String depa);
    public List getDepartamentos();
    public SocioBean getSocio(Long codigo);
    public boolean registrarCliente(ClienteBean socio) ;
    public boolean actualizarSocio(SocioBean socio);
    public boolean registrarClienteFamiliar(FamiliarBean socio);
    public List listarFamiliares(String codigo);
    public List listadoEdades();
    public List listarSociosB(int estado);
    public String getAportacion(Integer codigo);
    public List listarSociosR (String departamento,String provincia ,String distrito ,String sexo,String edad ,String estado,String condicion ,String centro,String situacion,String mr,String ar, String mi,String ai );
    public List listarOperaciones();
    public SocioBean getSocioporCliente(String codigo);
    public SocioBean getSocioporCliente2(String codigo) ;
    public FamiliarBean getFamiliarSocio(String cliente,int persona);
    public SocioBean getFamiliar(Integer codigo);
    public boolean actualizarIngreso(SocioBean socio);
    public List listarBeneficios(String socio,String desde,String hasta);
    public boolean registrarBeneficio(BeneficiosBean socio);
    public boolean eliminarBeneficio(Integer codigo);
    public List listarBeneficiosCaja(String socio,String desde,String hasta);
    public boolean actualizarBeneficios(int beneficio);
    public int registrarAportacion(AportacionBean aportacion);
    public int registrarCheque(ChequeBean socio);
    public int getAportacionFinal2(String codigo);
    public boolean actualizarAportacion(SocioBean socio);
    public boolean actualizarParentesco(FamiliarBean socio);
    public int registrarAportacionCope(AportacionBean aportacion);
    public List listarAportacionesCSocio(String codigo  );
   public boolean eliminarApo(AportacionBean aportacion);
    public AportacionBean getAporta(int socio, String periodo);
    public AportacionBean getAporta2(int socio, String periodo,String fechaPago);
    public AportacionBean getAportaCope2(int socio, String periodo,String fechaPago);
    public boolean actualizaAportacionCope2(AportacionBean aportacion,String fecha);
    public boolean actualizaAportacionN2(AportacionBean aportacion,String fecha);
    public boolean actualizaAportacionN(AportacionBean aportacion);
    public int actualizarDescuentoY(AportacionBean aportacion) ;
    public List listarSociosExportarProducto(String centro,String c,String mes,String ano,String producto,String tipoDesc,String fechaDesc);
    public List listarDescuentosProducto(String periodo,String ano,String condicion,String centro,String producto);
    
    public List listarDescuentosProducto0(String periodo,String ano,String tipo,String centro,String producto );
    
    public List listarDescuentos3Producto(String periodo,String ano,String tipo,String centro,String producto ) ;
    
    public int registrarSocioAuditoria(SocioBean socio);
    
    public int validaCodigo(String codigo);
    
    public AportacionBean getAporta(int aporta, int socio);
    
    public AportacionBean getAportaCope2(int aporte, int socio);
    
    public boolean actualizaAportacionN2(AportacionBean aportacion);
    
    public boolean eliminarApo(int apo, int socio);
    
    public boolean eliminarApoCope(int aportacion, int socio) ;
    
    public boolean actualizaAportacionCope2(AportacionBean aportacion);
    
    public boolean cancelaCompraDeuda(Integer codigo);
    
    public List listarFamiliaresBeneficiario(String documento, String codigo , String paterno, String materno);
    
    public List listarBeneficiosVal(String documento, String socio);
    
    public AportacionBean getDescuentoM(int socio, String periodo) ;
    
    public List listarSociosExportarProducto4(String centro, String c, 
                                             String mes, String ano, 
                                             String producto, String producto2, String producto3);
                                             
                                             public List listarDescuentosProductoMultiple(String periodo, String ano, 
                                                                                   String tipo, String centro, 
                                                                                   String producto2,String producto3,String producto4);
                                                                                   
                                                                                   public List listarDescuentos4(String periodo, String ano, String tipo, 
                                                                                                                 String centro) ;
    

                                                                                                                 public List listarDescuentosProducto4(String periodo, String ano, 
                                                                                                                                                      String condicion, String centro, 
                                                                                                                                                      String producto) ;
                                                                                                                                                      
                                                                                                                                            
                                                                                                                                                      public List listarBeneficiosCaja2(String socio, String desde, 
                                                                                                                                                                                       String hasta);
                                                                                                                                                      public List listarSociosExportarProducto2018(String centro, String c, 
                                                                                                                                                                                               String mes, String ano, 
                                                                                                                                                                                               String producto, String producto2, String producto3, String producto4, String producto5);
                                                                                                                                                                                               
                                                                                                                                                                                               
                                                                                                                                                                                               public List listarDescuentosProductoMultiple2018(String periodo, String ano, 
                                                                                                                                                                                                                                     String tipo, String centro, 
                                                                                                                                                                                                                                     String producto2,String producto3,String producto4,String producto5,String producto6,String producto7,String producto8,String producto9,String producto10);    
                                                                                                                                                                                                                                     
                                                                                                                                                                                                                                     public CronogramaBean getCuotaUno(String codigo) ;
                                                                                                                                                                                                                                     public AportacionBean getDescuentoCaja(String socio, String periodo) ;
                                                                                                                                                                                                                                     public AportacionBean getDescuentoCaja2(String socio);
                                                                                                                                                                                                                                     public List listarSociosExportarProducto2018Mona(String centro, String c, 
                                                                                                                                                                                                                                                                              String mes, String ano, 
                                                                                                                                                                                                                                                                              String producto, String producto2, String producto3, String producto4, String producto5, TarifaAnualBean tarifa);
                                                                                                                                                                                                                                                                              
                                                                                                                                                                                                                                                                              
                                                                                                                                                                                                                                                                              public List listarSociosExportarProducto2018MonaGraba(String centro, String c, 
                                                                                                                                                                                                                                                                                                                       String mes, String ano, 
                                                                                                                                                                                                                                                                                                                       String producto, String producto2, String producto3, String producto4, String producto5, String producto6, String producto7, String producto8, String producto9);
                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                                                                                                                                                                       public List listarDescuentosProductoMultiple2018M(String periodo, String ano, 
                                                                                                                                                                                                                                                                                                                                                             String tipo, String centro, 
                                                                                                                                                                                                                                                                                                                                                             String producto2,String producto3,String producto4,String producto5,String producto6,String producto7,String producto8,String producto9,String producto10);
                                                                                                                                                                                                                                                                                                                                                             
                                                                                                                                                                                                                                                                                                                                                             
                                                                                                                                                                                                                                                                                                                                                             
                                                                                                                                                                                                                                                                                                                                                             public List listarSociosExportarProducto2018MonaGraba2(String centro, String c, 
                                                                                                                                                                                                                                                                                                                                                                                                      String mes, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                      String producto, String producto2, String producto3, String producto4, String producto5 ,String producto6, String producto7, String producto8, String producto9);
                                                                                                                                                                                                                                                                                                                                                                                                      
                                                                                                                                                                                                                                                                                                                                                                                                      
                                                                                                                                                                                                                                                                                                                                                                                                      public List listarSociosExportarProducto2018Mona3(String centro, String c, 
                                                                                                                                                                                                                                                                                                                                                                                                                                               String mes, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                               String producto, String producto2, String producto3, String producto4, String producto5, TarifaAnualBean tarifa);
                                                                                                                                                                                                                                                                                                                                                                                                                                               
                                                                                                                                                                                                                                                                                                                                                                                                                                               public List listarSociosExportarProducto2019Mona(String centro,  
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        String mes, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        String producto, String producto2, String producto3, String producto4, String producto5, TarifaAnualBean tarifa) ;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        public List listarSociosExportarProducto2019Mona3(String centro,  
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 String mes, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 String producto, String producto2, String producto3, String producto4, String producto5 , TarifaAnualBean tarifa);
                                                                                                                                                                                                                                                                              
                                                                                                                                                                                                                                                                              
                                                                                                                                                                                                                                                

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 public List listarDescuentosProductoMultiple2019Temp(String periodo, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       String tipo, String centro, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       String producto2,String producto3,String producto4,String producto5,String producto6,String producto7,String producto8,String producto9,String producto10);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       public List listarDescuentosProductoMultiple2019tempo(String periodo, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             String tipo, String centro, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             String producto2,String producto3,String producto4,String producto5,String producto6) ;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             public AportacionBean getDescuentoTempM(int socio, String periodo) ;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             public List listarDescuentosProductoMultiple2018M(String periodo, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   String tipo, String centro, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   String producto2) ;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             public List listarDescuentosProductoMultipleError(String periodo, String ano, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   String tipo, String centro, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   String producto2,String producto3,String producto4,String producto5,String producto6);
																																																																																																																																																												                                                                                                   public List listarAportacionesSocio2019(String codigo);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       public List listarAportacioneCopeSocio2019(String codigo);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       public List listarTarifasTasaAnual();
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
    public boolean eliminarSocioDeuda(Integer codigo);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      

    public List listarSocios2(String tipo, String criterio, String estado);
    public SocioBean getSocioporClienteActivo(String codigo);
    public List listarSociosDeuda(String tipo, String criterio) ;
    public boolean eliminarSocioCastigo(Integer codigo);
    public boolean actualizarAporteFonbes(SocioBean socio);
    
    public ChequeBean getCheque(String codigo) ;
    public boolean eliminarCheque(Long cheque) ;
    public boolean actualizarCheque(ChequeBean cheque) ;
    public double getTotalDevoCoopamAnual(int cliente, String fecha) ;
    public boolean eliminarSocioCastigoDef(Integer codigo);
}
