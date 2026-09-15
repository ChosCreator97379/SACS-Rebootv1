package com.pe.sacs.vista.action;

import com.pe.sacs.logica.entidades.AportacionBean;
import com.pe.sacs.logica.entidades.BeneficiosBean;
import com.pe.sacs.logica.entidades.ChequeBean;
import com.pe.sacs.logica.entidades.CronogramaBean;
import com.pe.sacs.logica.entidades.MovimientoBean;
import com.pe.sacs.logica.entidades.MultaBean;
import com.pe.sacs.logica.entidades.PrestamoBean;
import com.pe.sacs.logica.entidades.RifaBean;
import com.pe.sacs.logica.entidades.SocioBean;
import com.pe.sacs.logica.prestamos.MantenimientoDAO;
import com.pe.sacs.logica.prestamos.n2t;
import com.pe.sacs.logica.socios.Util;
import com.pe.sacs.vista.SimuladorUtil;
import com.pe.sacs.vista.form.CajaForm;

import java.io.IOException;

import java.io.InputStream;

import java.sql.Connection;

import java.text.DecimalFormat;
import java.text.DecimalFormatSymbols;
import java.text.SimpleDateFormat;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import java.util.Map;

import javax.print.PrintService;

import javax.print.PrintServiceLookup;
import javax.print.attribute.HashPrintRequestAttributeSet;
import javax.print.attribute.PrintRequestAttributeSet;

import javax.print.attribute.standard.MediaSizeName;

import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import javax.servlet.http.HttpSession;

import javax.sql.DataSource;

import net.sf.jasperreports.engine.JRExporter;
import net.sf.jasperreports.engine.JRExporterParameter;
import net.sf.jasperreports.engine.JasperManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.JasperRunManager;

import net.sf.jasperreports.engine.export.JRPrintServiceExporter;
import net.sf.jasperreports.engine.export.JRPrintServiceExporterParameter;
import net.sf.jasperreports.engine.util.JRLoader;

import org.apache.struts.action.Action;
import org.apache.struts.action.ActionError;
import org.apache.struts.action.ActionErrors;
import org.apache.struts.action.ActionForm;
import org.apache.struts.action.ActionForward;
import org.apache.struts.action.ActionMapping;

import org.springframework.web.context.WebApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;

public class CrearCajaAction extends BaseAction {
    /**This is the main action called from the Struts framework.
     * @param mapping The ActionMapping used to select this instance.
     * @param form The optional ActionForm bean for this request.
     * @param request The HTTP Request we are processing.
     * @param response The HTTP Response we are processing.
     */
    public ActionForward execute(ActionMapping mapping, ActionForm form, 
                                 HttpServletRequest request, 
                                 HttpServletResponse response) throws IOException, 
                                                                      ServletException {
        ActionForward forward = new ActionForward(); // return value
        CajaForm actionForm = (CajaForm)form;
        
        ActionErrors errors = new ActionErrors();
        int accionMapa = 0;
        try {
        if  (actionForm.getAccion()!=null){
        if (actionForm.getAccion().equalsIgnoreCase("buscar"))
        accionMapa = buscar(request, actionForm);
        
        
            if (actionForm.getAccion().equalsIgnoreCase("buscarC"))
            accionMapa = buscarC(request, actionForm);
            
            if (actionForm.getAccion().equalsIgnoreCase("seleccionar"))
            accionMapa = seleccionar(request, actionForm);
        
        }
              
        
        if (actionForm.getAccion().equalsIgnoreCase("registrar"))
        {
        accionMapa = registrar(request,response, actionForm);
        }
            if (accionMapa == 0) {
            errors.add("subgrupo",
                    new ActionError("error.prestamo.problem9"));
            }
            
            if (accionMapa == 19) {
            errors.add("subgrupo",
                    new ActionError("error.familiar2018.problem"));
            }
            
        } catch (Exception e) {

        // Report the error using the appropriate name and ID.
        if (accionMapa == 0) {
        errors.add("subgrupo",
                new ActionError("error.prestamo.problem9"));
        }
        if (accionMapa == 11) {
        errors.add("subgrupo",
                new ActionError("error.cargarArchivo.problem"));
        }
        if (accionMapa == 12) {
        errors.add("subgrupo",
                new ActionError("error.eliminarArchivo.problem"));
        }
        if (accionMapa == 13) {
        errors.add("subgrupo",
                new ActionError("error.tamArchivo.problem"));
        }
        
            if (accionMapa == 19) {
            errors.add("subgrupo",
                    new ActionError("error.familiar2018.problem"));
            }

        }
        if (!errors.isEmpty()) {
        saveErrors(request, errors);
        }
        // Write logic determining how the user should be forwarded.
        switch (accionMapa) {
        case 1:
        forward = mapping.findForward("success"); //grabar mapa
        break;
        case 2:
        forward = mapping.findForward("success"); // agregar campo
        break;
        case 3:
        forward = mapping.findForward("success"); // agregar campo
         case 99:
         forward = mapping.findForward("success"); // agregar campo
          case 97:
          forward = mapping.findForward("success"); // agregar campo
          
           case 197:
           forward = mapping.findForward("success"); // agregar campo
        break;
            case 4:
            forward = mapping.findForward("exito"); // agregar campo
            break;
        default:
        forward = mapping.findForward("exito"); //no accion
        }
        // Finish with
        response.setHeader("Cache-Control","no-cache"); // HTTP 1.1
        response.setHeader("Pragma","no-store"); // HTTP 1.0
        response.setDateHeader ("Expires", -1); // Prevents caching at the proxy server
        return (forward);
        }

        private int buscar(HttpServletRequest request,
            CajaForm  crearform) throws Exception {
        int accionMapa = 1;
        String estado = "0";
            DecimalFormatSymbols dfs = new DecimalFormatSymbols();
                                  dfs.setDecimalSeparator('.'); 
                                  dfs.setGroupingSeparator(',');
                                  DecimalFormat df1 = new DecimalFormat("#0.00",dfs); 
                                  df1.setGroupingSize(3);
        try {
        String socio = crearform.getSocio().trim();
        
    SocioBean bean =    getCreditos().getSocioporCliente((crearform.getSocio()));
       if(bean!=null){
       request.setAttribute("nombres",bean.getNombres());
       request.setAttribute("documento",bean.getF01NumDoc());
       crearform.setNombres(bean.getNombres());
       
           String condicion = bean.getCondicion();
           String centro = bean.getStrCentro();
           String me = "OTROS";
           if(centro.equals("BANCO DE LA NACION")) { 
             if(condicion.equals("ACTIVO")) {
                me = "BN-A";
             }
             if(condicion.equals("PENSIONISTA")) {
                  me = "BN-P";
            }
                       
           }
           if(centro.equals("FEBAN")) {
                me = "FEBAN"; 
           }
           
           request.setAttribute("condicion", me);
       
      
      String     alerta = bean.getTitulo();
       String    obs = bean.getObs();
           request.setAttribute("alerta",alerta);
               request.setAttribute("obsd",obs);
       
       
           if(crearform.getOperacion().equals("11")){
           
           MultaBean multa = getCreditos().getMultaPendiente(new Long(crearform.getSocio()));
           if(multa!=null) {
               crearform.setMonto(String.valueOf(multa.getMultaSobregiro()));
               
               String periodo = multa.getPeriodo();
               if(periodo != null) {
               String mes = periodo.substring(0,2);
               String ano = periodo.substring(3);
               crearform.setPeriodo(mes);
               crearform.setPeriodo2(mes);
               crearform.setAno(ano);
               crearform.setChk(multa.getId());
               }
           }
           
           //crearform.setMonto(String.valueOf(bean.getMulta()));
               crearform.setAportacion("si");
           //crearform.setAportacion("obs");
            List listaa = getCreditos().listarMultas(crearform.getSocio(), "", "");
            if(listaa!=null){
            request.setAttribute("listadoMultas",listaa);
            }
            
           }
           
           if(crearform.getOperacion().equals("12")){ 
           
               RifaBean multa = getCreditos().getRifaPendiente(new Long(crearform.getSocio()));
               if(multa!=null) {
                   crearform.setMonto(String.valueOf(multa.getRifaSobregiro()));
                   String periodo = multa.getPeriodo();
                   if(periodo != null) {
                   String mes = periodo.substring(0,2);
                   String ano = periodo.substring(3);
                   crearform.setPeriodo(mes);
                   crearform.setPeriodo2(mes);
                   crearform.setAno(ano);
                   crearform.setChk(multa.getId());
                   }
               }
               
           crearform.setMonto(String.valueOf(bean.getRifa()));
               crearform.setAportacion("si");
           //    crearform.setAportacion("obs");
            List listaa = getCreditos().listarRifas(crearform.getSocio(), "", "");
            if(listaa!=null){
            request.setAttribute("listadoRifas",listaa);
            }
           }
           
           if(crearform.getOperacion().equals("13") || crearform.getOperacion().equals("1") || crearform.getOperacion().equals("9") || crearform.getOperacion().equals("2") || crearform.getOperacion().equals("10") || crearform.getOperacion().equals("6")  || crearform.getOperacion().equals("14")  || crearform.getOperacion().equals("15")  || crearform.getOperacion().equals("16") ){           
           crearform.setAportacion("obs");
           }
       
       
       }
       
       
       
              
       if(crearform.getOperacion().equals("2")||crearform.getOperacion().equals("6") ||crearform.getOperacion().equals("10") ){
           List listadoP = getCreditos().getPrestamos("1",String.valueOf(crearform.getSocio()),"4");
           
           
           request.setAttribute("listadoPrestamos2",listadoP);
           request.setAttribute("opera","si");
       }
       
       socio = String.valueOf(new Long(crearform.getSocio().trim()));
       
                   
       if(crearform.getOperacion().equals("1")){
           String ano = crearform.getAno();
           String fec = crearform.getPeriodo();
               String ano2 = crearform.getAno();
               String fec2 = crearform.getPeriodo2();
               String dia = "20";    
           int p = new Integer(fec);
           int p2 = new Integer(fec2);
           int d = p2 - p;
           if(d ==0){
           
               int de=      getCreditos().getAportacionFinal2((crearform.getSocio()));
                 
                 int todiato = 0;
           int apecope = 0;
           
           apecope = new Integer(bean.getAporteCope());
                 todiato = new Integer(bean.getAportacion()) + de ;
           
           crearform.setApecope(String.valueOf(apecope));
               crearform.setApeno(String.valueOf(new Integer(bean.getAportacion())));
           
           crearform.setMonto(String.valueOf(todiato));
           }
           if(d>0){
           int suma = 0;
           //String s = bean.getAportacionCS();
           
             //  int p32 = s.indexOf(".");
            //    if(p32!=-1){           
            //    s = s.substring(0,p32);
            //    }
             int de=      getCreditos().getAportacionFinal2((crearform.getSocio()));
               
               int s = 0;
               int apecope2 = 0;
               apecope2 = new Integer(bean.getAporteCope());
               s = new Integer(bean.getAportacion()) + de ;
            
            
           suma = ((d+1) * new Integer(s)) ;
           crearform.setApecope(String.valueOf((d+1) * apecope2));
               crearform.setApeno(String.valueOf(((d+1) * new Integer(s))));
           
           
           crearform.setMonto(String.valueOf(suma));
           }
           
      crearform.setAportacion("si");
      List listaa = getCreditos().listarAportacionesSocio(crearform.getSocio());
      if(listaa!=null){
      request.setAttribute("listadoAportaciones",listaa);
      }
       }
       
            // PAGO SOBREGIRO FONBES
            if(crearform.getOperacion().equals("14")){
   
            crearform.setMonto("0.0");
                
            crearform.setAportacion("si");
            
                List listaa = getCreditos().listarAportacionesSocio(crearform.getSocio());
                if(listaa!=null){
                double yu =    getCreditos().getSobgregiroFonbesTotal(new Long(crearform.getSocio()));
                    request.setAttribute("sobregiroApo",yu);  
                    if(yu >0) {
                                      crearform.setSobreAportacion(df1.format(yu).toString());
                                                             crearform.setMonto(df1.format(yu).toString());
                                      }
             request.setAttribute("listadoAportacionesFonbes",listaa);  
                }
    
            }
            
            if(crearform.getOperacion().equals("15")){
            
                crearform.setMonto("0.0");
                    
                crearform.setAportacion("si");
                
                    List listaa = getCreditos().listarAportacionesCSocio(crearform.getSocio());
                    if(listaa!=null){
                    double yu =    getCreditos().getSobgregiroCoopamTotal(new Long(crearform.getSocio()));
                        request.setAttribute("sobregiroApo",yu);  
                        if(yu >0) {
                                          crearform.setSobreAportacion(df1.format(yu).toString());
                                                                 crearform.setMonto(df1.format(yu).toString());
                                          }
                 request.setAttribute("listadoAportacionesFonbes",listaa);  
                    }
            }
       
       
            if(crearform.getOperacion().equals("9")){
                String ano = crearform.getAno();
                String fec = crearform.getPeriodo();
                    String ano2 = crearform.getAno();
                    String fec2 = crearform.getPeriodo2();
                    String dia = "20";    
                int p = new Integer(fec);
                int p2 = new Integer(fec2);
                int d = p2 - p;
                if(d ==0){
                
                    int de=      new Integer(bean.getAporteCope());
                      
                      int todiato = 0;
                int apecope = 0;
                
               
                      todiato = new Integer( de);
                
                crearform.setApecope(String.valueOf(de));
                    crearform.setApeno(String.valueOf(new Integer(bean.getAporteCope())));
                
                crearform.setMonto(String.valueOf(todiato));
                }
                if(d>0){
                int suma = 0;
                //String s = bean.getAportacionCS();
                
                  //  int p32 = s.indexOf(".");
                 //    if(p32!=-1){           
                 //    s = s.substring(0,p32);
                 //    }
                  int de=       new Integer(bean.getAporteCope());
                    
                    int s = 0;
                    int apecope2 = 0;
                    apecope2 = new Integer(bean.getAporteCope());
                    s =  de ;
                 
                 
                suma = ((d+1) * new Integer(s));
               
                crearform.setApecope(String.valueOf((d+1) * apecope2));
                crearform.setApeno(String.valueOf(((d+1) * new Integer( apecope2))));
                
                
                crearform.setMonto(String.valueOf(suma));
                }
                
            crearform.setAportacion("si");
            List listaa = getCreditos().listarAportacionesCSocio(crearform.getSocio());
            if(listaa!=null){
            request.setAttribute("listadoAportaciones",listaa);
            }
            }
       
       
            if(crearform.getOperacion().equals("4")){            
            List listafonbes = getCreditos().listarAportacionesSocio2019(crearform.getSocio());
            if(listafonbes!=null){
            double to = 0;
            for(int i = 0 ; i < listafonbes.size() ; i++){
            AportacionBean mov = new AportacionBean();
            mov = (AportacionBean) listafonbes.get(i);
            to = to + Double.parseDouble(mov.getAportacionD());
            }
            crearform.setMonto(String.valueOf(to));
            request.setAttribute("listafonbes",listafonbes);
            crearform.setAportacion("obs");
            }
            }
            
            if(crearform.getOperacion().equals("5")){
                           crearform.setDniAportacion(bean.getF01NumDoc());
                       List listacoopam = getCreditos().listarAportacioneCopeSocio2019(crearform.getSocio());
                       String fechaDesde = "";
                       String fechaHasta = "";
                       int ttt = 0;
                       ttt = listacoopam.size();
                       if(listacoopam!=null){
                           double montoAp = getCreditos().getTotalDevoCoopam(new Integer(crearform.getSocio())); 
                           double to2 = 0;
                           for(int i = 0 ; i < listacoopam.size() ; i++){
                               AportacionBean mov = new AportacionBean();
                               mov = (AportacionBean) listacoopam.get(i);
                               to2 = to2 + Double.parseDouble(mov.getAportacionD());
                           if(i == 0 ){fechaDesde = mov.getFecha();}
                           if( i == (ttt-1) ){fechaHasta = mov.getFecha();}
                           }
                           double f = to2 - montoAp;
                           crearform.setMonto(String.valueOf(f));
                           crearform.setObs("PERIODO: "+fechaHasta + " AL: " + fechaDesde);
                           crearform.setAportacion("obs");
                       request.setAttribute("listacoopam",listacoopam);
                       
                       List listadoF = getCreditos().listarFamiliaresT((crearform.getSocio().trim()));
                           List lista = new ArrayList();
                           for(int i = 0 ; i < listadoF.size() ; i++){
                           SocioBean mov = new SocioBean();
                           mov = (SocioBean) listadoF.get(i);
                           String n = mov.getNombres() + " ("+mov.getParentesco()+")";
                           mov.setNombres(n);
                           lista.add(mov);
                           }
                       request.setAttribute("listadoFamiliares",lista);
                       request.setAttribute("beneficiario","si");
                       request.setAttribute("motivo","si");
                       }
                       }

            if(crearform.getOperacion().equals("3")){
                List listadoP = getCreditos().getPrestamos("1",String.valueOf((crearform.getSocio())),"2");
                if(listadoP!=null && listadoP.size() > 0){
                PrestamoBean n = (PrestamoBean) listadoP.get(0);
                if(n!=null){
                crearform.setMonto(n.getDesembolso());
                crearform.setChk(n.getCodigo());
                request.setAttribute("listadoPrestamos2",listadoP);
                request.setAttribute("opera2","si");
                    request.setAttribute("cheque","si"); 
                    request.setAttribute("beneficio","si"); 
                 crearform.setNombres(bean.getNombres());
                    crearform.setAportacion("obs");
                }
                }
            }
       
            if(crearform.getOperacion().equals("7")){
            List listadoBene = getCreditos().listarBeneficiosCaja2(String.valueOf(crearform.getSocio()),"","");
            BeneficiosBean beneficio = (BeneficiosBean) listadoBene.get(0);
            
            if(beneficio!=null){
                    
            crearform.setMonto(beneficio.getImporte());
            request.setAttribute("beneficio","si");  
                request.setAttribute("cheque","si");  
            crearform.setChk(beneficio.getDocu().trim());
                request.setAttribute("opera5","si");
                crearform.setNombres(beneficio.getBeneficiario());   
            }
            
            
            
            }
            crearform.setSocio(bean.getF01Cliente());
            crearform.setOperacion(crearform.getOperacion());
            crearform.setObs(crearform.getObs());
       crearform.setMonto(crearform.getMonto()); 
        } catch (Exception e) {
        e.printStackTrace();
        accionMapa = 1;
        }
        return accionMapa;
        }

    private int buscarC(HttpServletRequest request,
        CajaForm  crearform) throws Exception {
    int accionMapa = 97;
    try {
        DecimalFormatSymbols dfs = new DecimalFormatSymbols();
        dfs.setDecimalSeparator('.'); 
        dfs.setGroupingSeparator(',');
        DecimalFormat df1 = new DecimalFormat("#0.00",dfs); 
        df1.setGroupingSize(3);
    
    String socio = crearform.getSocio().trim();
    String chk = null;
    String id3 [] = crearform.getChk3();
    
    SocioBean bean =    getCreditos().getSocioporCliente((socio));
    if(bean!=null){
    request.setAttribute("nombres",bean.getNombres());
    request.setAttribute("documento",bean.getF01NumDoc());
    
        String condicion = bean.getCondicion();
        String centro = bean.getStrCentro();
        String me = "OTROS";
        if(centro.equals("BANCO DE LA NACION")) { 
          if(condicion.equals("ACTIVO")) {
             me = "BN-A";
          }
          if(condicion.equals("PENSIONISTA")) {
               me = "BN-P";
         }
                    
        }
        if(centro.equals("FEBAN")) {
             me = "FEBAN"; 
        }
        
        request.setAttribute("condicion", me);
        
    }
    
    crearform.setSocio(crearform.getSocio());
    crearform.setOperacion(crearform.getOperacion());
        crearform.setObs(crearform.getObs());
        crearform.setSobreApo(crearform.getChk());
    
        if(crearform.getOperacion().equals("14")){
            double totalis = 0;
            for(int i = 0; i < id3.length ; i++){
            chk = id3[i];
            int raya = chk.indexOf("-");
             String soc = chk.substring(0,raya);
             int pre = chk.indexOf("?");
             int pre2 = chk.indexOf("_");
             int pre3 = chk.indexOf("|");
             int pre4 = chk.indexOf("#");
            
            String perido = chk.substring(raya+1, pre);
            String apop = chk.substring(pre+1,pre2);
            String apod = chk.substring(pre2+1,pre3);
            String d = chk.substring(pre3+1,pre4);
            String fp = chk.substring(pre4+1);
            
            double fija = 0;
            double def = 0;
            double t = 0;
            
            fija = Double.parseDouble(d);
            def = Double.parseDouble(apod);
            t = fija -def;
            totalis = totalis + t;
            //crearform.setMonto(df1.format(t).toString());
            crearform.setNombres(crearform.getNombres());
           
            crearform.setObs("PAGO DE SOBREGIRO DE APORTACION FONBES POR EL PERIODO: " + perido);
            }
                crearform.setMonto(df1.format(totalis).toString());
                List listaa = getCreditos().listaSobregiroFonbesSocio(crearform.getSocio());
                if(listaa!=null){
                
                crearform.setChk3(crearform.getChk3());
                request.setAttribute("listadoAportacionesFonbes",listaa);
                }
            }
        
        if(crearform.getOperacion().equals("15")){
        
            int raya = chk.indexOf("-");
             String soc = chk.substring(0,raya);
             int pre = chk.indexOf("?");
             int pre2 = chk.indexOf("_");
             int pre3 = chk.indexOf("|");
             int pre4 = chk.indexOf("#");
            
            String perido = chk.substring(raya+1, pre);
            String apop = chk.substring(pre+1,pre2);
            String apod = chk.substring(pre2+1,pre3);
            String d = chk.substring(pre3+1,pre4);
            String fp = chk.substring(pre4+1);
            
            double fija = 0;
            double def = 0;
            double t = 0;
            
            fija = Double.parseDouble(d);
            def = Double.parseDouble(apod);
            t = fija -def;
            crearform.setMonto(df1.format(t).toString());
            crearform.setNombres(crearform.getNombres());
           
            crearform.setObs("PAGO DE SOBREGIRO DE APORTACION COOPAM POR EL PERIODO: " + perido);
        }
    
        if(crearform.getOperacion().equals("10")){
            PrestamoBean pres = getCreditos().getPrestamo(new Integer(crearform.getChk()));
            if(pres!=null){
                double gastosa = 0;
                double mora = 0;
                double otros1 = 0;
                double otros2 = 0;
                double total = 0;
                if(pres.getGastosa()!=null){
                gastosa = Double.parseDouble(pres.getGastosa());
                }
                if(pres.getMora()!=null){   
                mora =  Double.parseDouble( pres.getMora());
                }
                if(pres.getOtros()!=null){   
                otros1 = Double.parseDouble(pres.getOtros());            
                }
                if(pres.getOtros2()!=null){
                otros2 = Double.parseDouble(  pres.getOtros2());
                }
                
                crearform.setGastosMora(df1.format(mora).toString());
                
                crearform.setOtros1(df1.format(otros1).toString());
                crearform.setOtros2(df1.format(otros2).toString());         
                crearform.setGastosAdm(df1.format(gastosa).toString());
                
                double t = otros1 + otros2 + gastosa + mora + Double.parseDouble(pres.getAportacion());  
                
                crearform.setAporte1("0.0"); 
                crearform.setAporte2("0.0"); 
            crearform.setMonto(String.valueOf(t));
            crearform.setSobregiro(pres.getAportacion());
            
            
            crearform.setOperacion(crearform.getOperacion());
        request.setAttribute("sobregiro","si");
    
            }
            
            
        }
        
        
    if(crearform.getOperacion().equals("2") || crearform.getOperacion().equals("6")  || crearform.getOperacion().equals("10")){
       List listadoP = getCreditos().getPrestamos("1",(socio),"4");
       request.setAttribute("listadoPrestamos2",listadoP);
       request.setAttribute("opera","si");
    }
    
        if(crearform.getOperacion().equals("2")){
        
      
        double gastosa = 0;
        double mora = 0;
        double otros1 = 0;
        double otros2 = 0;
        double total = 0;
            String pasa = "";
            String dia="";
            String mes="";
            String ano2="";
      PrestamoBean pres = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                    String fecha = pres.getFechaUP();
       
                       if(fecha!=null){
                        int puto = fecha.indexOf("/");
                        int puto2 = fecha.lastIndexOf("/");
                        dia = fecha.substring(0,puto);
                        mes = fecha.substring(puto+1,puto2);
                        ano2 = fecha.substring(puto2+1);
                       pasa = ano2 + "-"+ mes+"-" + dia;
                       }              
            
            if(Double.parseDouble(pres.getAportacion() ) > 0) {
             
                 request.setAttribute("alerta","Socio con sobregiro!!");
                     request.setAttribute("obsd","Socio tiene Sobregiro");
             
             }
             
            int cuotasto = new Integer(pres.getCuotas());
            int cuotaspen = new Integer(pres.getCuotasPen());            
            int dife = cuotasto - cuotaspen;
            if(dife > 0) {
            if(bean.getCondicion().equals("ACTIVO"))  pasa = ano2 + "-"+ mes+"-" + "20";
                if(bean.getCondicion().equals("PENSIONISTA"))  pasa = ano2 + "-"+ mes+"-" + "10";
            
            }
            
         List listadoP2 = getCreditos().listarCuotasCaja(new Integer(crearform.getChk()), pasa );
        
//        SimuladorUtil util = new SimuladorUtil ();
//        String cuota = "0";
//            String dia = "";
//            String mes = "";
//            String ano2 = "";
//            String cuotaM = "";
//            String s = "";
//            String fechaBusca = "";
//            boolean flag = false;    
//            CronogramaBean co = null;
//        PrestamoBean pres = getCreditos().getPrestamo(new Integer(crearform.getChk()));
//            CronogramaBean co2 = null;
//        if(pres!=null){
//        
//            if(pres.getGastosa()!=null){
//            gastosa = Double.parseDouble(pres.getGastosa());
//            }
//            if(pres.getMora()!=null){   
//            mora =  Double.parseDouble( pres.getMora());
//            }
//            if(pres.getOtros()!=null){   
//            otros1 = Double.parseDouble(pres.getOtros());            
//            }
//            if(pres.getOtros2()!=null){
//            otros2 = Double.parseDouble(  pres.getOtros2());
//            }
//        
//                 
//            List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaDes()) );
//         //   List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaR()) );
//            if(listadoH!=null){
//            request.setAttribute("listadoPagos",listadoH);
//            
//            }
//            
//            
//            
//        
//        
//          
//        String fecha = pres.getFechaUP();
//       
//           if(fecha!=null){
//            int puto = fecha.indexOf("/");
//            int puto2 = fecha.lastIndexOf("/");
//            dia = fecha.substring(0,puto);
//            mes = fecha.substring(puto+1,puto2);
//            ano2 = fecha.substring(puto2+1);
//              
//           }
//            cuota =pres.getCuotaMensual();   
//            
//            s = pres.getSaldo();
//        
//             co = getCreditos().getCuotaUno(pres.getCodigo());
//            
//            String fechaVen =  co.getFechaStr();
//            
//       
//            
//            flag = validaPeriodo(fechaVen, fecha);
//            
//     
//        
//        }
//        
//      
//     
//       
//       if(flag == true)
//       
//           {co2 =  co;
//           }
//     
//        else{
//            if(new Integer(mes) <12)  mes = String.valueOf(new Integer(mes) +1);
//            if(mes.length() == 1) mes = "0" + mes;
//            if( new Integer(mes) == 12) mes = "01";
//            dia = pres.getFechaPago();
//            
//            fechaBusca = ano2 +"-" + mes + "-" +dia;
//        
//            co2 =     getCreditos().getCronograma(pres.getCodigo(),fechaBusca);
//              
//        }
//           
//           
//           if(co2!=null){
//           CronogramaBean c = co2;
// 
//           crearform.setFechaVen( c.getFechaStr());
//           
//
//           double amor = (Double.parseDouble(c.getAmortizacion())) ;
//           
//           crearform.setAmortiza(c.getAmortizacion());
//           crearform.setChk(crearform.getChk());
//         
//           
//           double gasAdm = Double.parseDouble(c.getInteres());
//           if(gasAdm >0){
//           gasAdm = gasAdm + gastosa;
//           }
//           else {
//               gasAdm = 0.0;
//           }
//           
//           crearform.setGastosMora(df1.format(mora).toString());
//           
//           crearform.setOtros1(df1.format(otros1).toString());
//           crearform.setOtros2(df1.format(otros2).toString());         
//          crearform.setGastosAdm(df1.format(gasAdm).toString()); 
//           
//           double so = 0;
//           String so1 = pres.getAportacion();
//           if(so1!=null){
//           so = Double.parseDouble(so1);
//           }
//           
//          total = gasAdm + mora + amor + otros1 + otros2 + so;
//           
//           
//               crearform.setMonto(df1.format(total).toString());
//               crearform.setSobregiro(pres.getAportacion());
//               request.setAttribute("amortiza","si");
//           } 
//        
//        
        
        
        
           request.setAttribute("listadoCrono2",listadoP2);
           request.setAttribute("cuotas",pres.getCuotasPen());
           
           
           crearform.setMonto(crearform.getMonto());
        
        
                        List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaDes()) );
                     //   List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaR()) );
                        if(listadoH!=null){
                        request.setAttribute("listadoPagos",listadoH);
            //            
                        }
        
        
       // request.setAttribute("amortiza","si");
        
        
        
        }
        
        if(crearform.getOperacion().equals("6")){
        
        PrestamoBean bean3 = getCreditos().getPrestamo(new Integer(crearform.getChk()));
        if(bean3!=null){
        request.setAttribute("cancelacion","si");
        //request.setAttribute("saldo",bean3.getSaldo());
        crearform.setAmortiza(bean3.getSaldo());
        //request.setAttribute("gastosa",bean3.getGastosa());
        crearform.setGastosAdm(bean3.getGastosa());
        //request.setAttribute("gastosm",bean3.getMora());
        crearform.setGastosMora(bean3.getMora());
       // request.setAttribute("otros1",bean3.getOtros());
        crearform.setOtros1(bean3.getOtros());
        //request.setAttribute("otros2",bean3.getOtros2());
        crearform.setOtros2(bean3.getOtros2());
        crearform.setSobregiro(bean3.getAportacion());
        
        
        request.setAttribute("cuotasPend",bean3.getCuotasPen());
                crearform.setMonto(String.valueOf(bean3.getTotalF()));
            request.setAttribute("amortiza","si");
        
        }
        
        
//            List listadoP2 = getCreditos().listarCuotas(new Integer(crearform.getChk()));
//            double suma = 0.0;
//            if(listadoP2.size()>0){
//            for(int i = 0; i < listadoP2.size() ; i++){
//            CronogramaBean cro = new CronogramaBean();
//            cro = (CronogramaBean) listadoP2.get(i);
//            suma = suma + Double.parseDouble(cro.getCuota());            
//            }            
//            crearform.setMonto(String.valueOf(suma));
//            request.setAttribute("cuotas",listadoP2.size());
//            }
        }
        
        
    
 
    } catch (Exception e) {
    e.printStackTrace();
    accionMapa = 0;
    }
    return accionMapa;
    }
    
    
    private int seleccionar(HttpServletRequest request,
        CajaForm  crearform) throws Exception {
    int accionMapa = 197;
    try {
        DecimalFormatSymbols dfs = new DecimalFormatSymbols();
        dfs.setDecimalSeparator('.'); 
        dfs.setGroupingSeparator(',');
        DecimalFormat df1 = new DecimalFormat("#0.00",dfs); 
        df1.setGroupingSize(3);
    
    String socio = crearform.getSocio().trim();
    String chk = null;
    String id3 [] = crearform.getChk3();
    
    SocioBean bean =    getCreditos().getSocioporCliente((socio));
    if(bean!=null){
    request.setAttribute("nombres",bean.getNombres());
    request.setAttribute("documento",bean.getF01NumDoc());
    }
    
    crearform.setSocio(crearform.getSocio());
    crearform.setOperacion(crearform.getOperacion());
        crearform.setObs(crearform.getObs());
        crearform.setSobreApo(crearform.getChk());
               
        
    if(crearform.getOperacion().equals("2") ){
       List listadoP = getCreditos().getPrestamos("1",(socio),"4");
       request.setAttribute("listadoPrestamos2",listadoP);
       request.setAttribute("opera","si");
    }
    
        if(crearform.getOperacion().equals("2")){
        
      
        double gastosa = 0;
        double mora = 0;
        double otros1 = 0;
        double otros2 = 0;
        double total = 0;
            String pasa = "";
            String dia = "";
            String mes = "";
            String ano2 = "";    
        
      PrestamoBean pres = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                    String fecha = pres.getFechaUP();

                       if(fecha!=null){
                        int puto = fecha.indexOf("/");
                        int puto2 = fecha.lastIndexOf("/");
                        dia = fecha.substring(0,puto);
                        mes = fecha.substring(puto+1,puto2);
                        ano2 = fecha.substring(puto2+1);
                       pasa = ano2 + "-"+ mes+"-" + dia;
                       }       
                       
            int cuotasto = new Integer(pres.getCuotas());
            int cuotaspen = new Integer(pres.getCuotasPen());            
            int dife = cuotasto - cuotaspen;
            if(dife > 0) {
            if(bean.getCondicion().equals("ACTIVO"))  pasa = ano2 + "-"+ mes+"-" + "20";
                if(bean.getCondicion().equals("PENSIONISTA"))  pasa = ano2 + "-"+ mes+"-" + "10";
            
            }              
            
         List listadoP2 = getCreditos().listarCuotasCaja(new Integer(crearform.getChk()), pasa );
        
    //        SimuladorUtil util = new SimuladorUtil ();
    //        String cuota = "0";
    //            String dia = "";
    //            String mes = "";
    //            String ano2 = "";
    //            String cuotaM = "";
    //            String s = "";
    //            String fechaBusca = "";
    //            boolean flag = false;
    //            CronogramaBean co = null;
    //        PrestamoBean pres = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                CronogramaBean co2 = null;
    //        if(pres!=null){
    //
                if(pres.getGastosa()!=null){
                gastosa = Double.parseDouble(pres.getGastosa());
                }
                if(pres.getMora()!=null){
                mora =  Double.parseDouble( pres.getMora());
                }
                if(pres.getOtros()!=null){
                otros1 = Double.parseDouble(pres.getOtros());
                }
                if(pres.getOtros2()!=null){
                otros2 = Double.parseDouble(  pres.getOtros2());
                }
                
            if(Double.parseDouble(pres.getAportacion() ) > 0) {
             
                 request.setAttribute("alerta","Socio con sobregiro!!");
                     request.setAttribute("obsd","Socio tiene Sobregiro");
             
             }
    //
    //
    //            List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaDes()) );
    //         //   List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaR()) );
    //            if(listadoH!=null){
    //            request.setAttribute("listadoPagos",listadoH);
    //
    //            }
    //
    //
    //
    //
    //
    //
    //        String fecha = pres.getFechaUP();
    //
    //           if(fecha!=null){
    //            int puto = fecha.indexOf("/");
    //            int puto2 = fecha.lastIndexOf("/");
    //            dia = fecha.substring(0,puto);
    //            mes = fecha.substring(puto+1,puto2);
    //            ano2 = fecha.substring(puto2+1);
    //
    //           }
    //            cuota =pres.getCuotaMensual();
    //
    //            s = pres.getSaldo();
    //
    //             co = getCreditos().getCuotaUno(pres.getCodigo());
    //
    //            String fechaVen =  co.getFechaStr();
    //
    //
    //
    //            flag = validaPeriodo(fechaVen, fecha);
    //
    //
    //
    //        }
    //
    //
    //
    //
    //       if(flag == true)
    //
    //           {co2 =  co;
    //           }
    //
    //        else{
    //            if(new Integer(mes) <12)  mes = String.valueOf(new Integer(mes) +1);
    //            if(mes.length() == 1) mes = "0" + mes;
    //            if( new Integer(mes) == 12) mes = "01";
    //            dia = pres.getFechaPago();
    //
    //            fechaBusca = ano2 +"-" + mes + "-" +dia;
    //
                co2 =     getCreditos().getCronogramaCaja(pres.getCodigo(), crearform.getChk2());
    //
    //        }
    //
    //
               if(co2!=null){
    //           CronogramaBean c = co2;
    //
               crearform.setFechaVen( co2.getFechaStr());
    //
    //
               double amor = (Double.parseDouble(co2.getAmortizacion())) ;
    //
               crearform.setAmortiza(co2.getAmortizacion());
               crearform.setChk(crearform.getChk());
               crearform.setChk2(crearform.getChk2());
    //
    //
              double gasAdm = Double.parseDouble(co2.getInteres());
               if(gasAdm >0){
               gasAdm = gasAdm + gastosa;
               }
               else {
                   gasAdm = 0.0;
               }
    
               crearform.setGastosMora(df1.format(mora).toString());
    //
               crearform.setOtros1(df1.format(otros1).toString());
               crearform.setOtros2(df1.format(otros2).toString());
              crearform.setGastosAdm(df1.format(gasAdm).toString());
    //
               double so = 0;
               String so1 = pres.getAportacion();
               if(so1!=null){
               so = Double.parseDouble(so1);
               }
    //
   //           total = gasAdm + mora + amor + otros1 + otros2 + so;
    //
    //
                  crearform.setMonto(co2.getCuota().toString());
                   crearform.setSobregiro(pres.getAportacion());
                   request.setAttribute("amortiza2","si");
               }
    //
    //
        
        
        
           request.setAttribute("listadoCrono2",listadoP2);
           request.setAttribute("cuotas",pres.getCuotasPen());
           
           
           crearform.setMonto(co2.getCuota());
      
            List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaDes()) );
            //   List listadoH = getCreditos().getPamortizaciones(socio, getFecha(pres.getFechaR()) );
            if(listadoH!=null){
            request.setAttribute("listadoPagos",listadoH);
            //
            }
        
        
     //   request.setAttribute("amortiza","si");
        
        
        
        }
        
              
        
    
    
    } catch (Exception e) {
    e.printStackTrace();
    accionMapa = 0;
    }
    return accionMapa;
    }
    
        private int registrar(HttpServletRequest request, HttpServletResponse response,
            CajaForm  crearform) throws Exception {
  
  double sobregiro = 0;
  double cuotasS = 0;
  double montoA = 0;
            int accionMapa = 2;
            int estado = 0;
  Integer producto = null;
  String strEstado = "HABIL"; 
  //recibes el monto de caja
   double r = 0;
   String  persona = "";
  montoA = Double.parseDouble(crearform.getMonto());
            SocioBean bean = null;
    try{   
            bean =    getCreditos().getSocioporCliente((crearform.getSocio().trim()));
            strEstado = bean.getF01StdoPersn();
  //     if(strEstado.equals("HABIL") || (strEstado.equals("BAJA") && crearform.getOperacion().equals("5")) || (strEstado.equals("BAJA") && crearform.getOperacion().equals("7")) ) {     
            persona = bean.getF01IdPersona();
            
            MovimientoBean mov = new MovimientoBean();
            mov.setMonto(crearform.getMonto()); 
           String val = null;
        String obs= "";
        String obs2 = "";
            obs2 = crearform.getObs();
            if(crearform.getOperacion().equals("4")){
            obs = crearform.getObs();
                mov.setMonto(crearform.getMonto());
            }
            
            if(crearform.getOperacion().equals("5")){
                         mov.setMonto(crearform.getMonto());
                         obs = crearform.getObs();
                         String otrosss = null;
                         otrosss = crearform.getPelusa();
                         if(otrosss!=null && !otrosss.equals("")){
                         mov.setBene(crearform.getPelusa());
                         }
                         else{
                         mov.setBene(crearform.getBeneficiario());
                         }
                         mov.setDni(crearform.getDniAportacion());
                         mov.setDevolucion(crearform.getDevolucion());
                         mov.setStd(crearform.getStd());
                     }
            
            if(crearform.getOperacion().equals("11")){
                obs = crearform.getObs();
                mov.setMonto(crearform.getMonto());
                MultaBean rifa = getCreditos().getMultaPendiente(new Long(crearform.getSocio()));
                if(rifa != null && ( new Double(rifa.getMultaSobregiro()) > 0 )  ) {
                    MultaBean multa = new MultaBean();
                    multa.setId(rifa.getId());
                    multa.setFechaPago(getFechaActual());
                    multa.setMultaDefinitiva(crearform.getMonto());
                        if(rifa!=null) {
                        double sobre = new Double(rifa.getMultaSobregiro());
                        double resta = 0;
                        if(sobre > 0) {
                            resta = sobre - new Double(crearform.getMonto());
                            multa.setMultaSobregiro(String.valueOf(resta));
                        }
                        
                    }
                   getCreditos().actualizaMultaCaja(multa);    
                }
            }
            
            if(crearform.getOperacion().equals("12")){
                obs = crearform.getObs();
                mov.setMonto(crearform.getMonto());
                RifaBean rifa = getCreditos().getRifaPendiente(new Long(crearform.getSocio()));
                if(rifa != null && ( new Double(rifa.getRifaSobregiro()) > 0 )  ) {
                    RifaBean multa = new RifaBean();
                    multa.setId(rifa.getId());
                    multa.setFechaPago(getFechaActual());
                    multa.setRifaDefinitiva(crearform.getMonto());
                        if(rifa!=null) {
                        double sobre = new Double(rifa.getRifaSobregiro());
                        double resta = 0;
                        if(sobre > 0) {
                            resta = sobre - new Double(crearform.getMonto());
                            multa.setRifaSobregiro(String.valueOf(resta));
                        }
                        
                    }
                   getCreditos().actualizaRifaCaja(multa);    
                }
            }
            
            if(crearform.getOperacion().equals("13")){
                obs = crearform.getObs();
                mov.setMonto(crearform.getMonto());
            }
            
           if(crearform.getOperacion().equals("16")){
               obs = crearform.getObs();
               mov.setMonto(crearform.getMonto());
           }
        
      if(crearform.getOperacion().equals("2")){
      mov.setObs2(obs2);
      PrestamoBean amortizap = new PrestamoBean();
     
      PrestamoBean be = getCreditos().getPrestamo(new Integer(crearform.getChk()));
     CronogramaBean co2 =   getCreditos().getCronogramaCaja(be.getCodigo(), crearform.getChk2());
     
     //cuotas pendientes     
     int cuotas =  new Integer(be.getCuotasPen());
     cuotas = cuotas -1;
     amortizap.setCuotasPen(String.valueOf(cuotas));
     amortizap.setCodigo(crearform.getChk());
     amortizap.setCuotaMensual(co2.getCuota());
     double gaactual = be.getIntereses();
     double s = new Double(be.getAportacion());
          double j  = 0;
         int ind = 0;
       
      producto = new Integer(be.getProducto());     
     sobregiro = Double.parseDouble(co2.getCuota()) - montoA;
     
     if(sobregiro > 0){
         if(s  > 0){
         if(montoA == s){ 
             
             amortizap.setAportacion(String.valueOf("0"));
          
             montoA = 0;   
             j = s;
             getCreditos().actualizarSobregiro(amortizap);
             ind = 1;
         }
           
             if(montoA < s){ 
                  j = s - montoA;   
                 amortizap.setAportacion(String.valueOf(j));
                 getCreditos().actualizarSobregiro(amortizap);
                j = montoA;
                 montoA = 0;  
                 ind = 1;
             }
         
         }
        if(s == 0){
         amortizap.setAportacion(String.valueOf(sobregiro));
         }
     }
     else{
         amortizap.setAportacion(String.valueOf("0"));
     }
     
     String saldo = be.getSaldo();
    // String ga =     be.getGastosa();
    // String gm =     be.getMora();
    // String o1 = be.getOtros();
    // String o2 = be.getOtros2();
     String ga =     crearform.getGastosAdm();
     String gm =     crearform.getGastosMora();
     String o1 = crearform.getOtros1();
     String o2 = crearform.getOtros2();
     
     double amortiza = 0;     
     double gami  = 0;
    double gamo = 0;
    double otros1 = 0;
    double otros2 = 0;
    double gP = 0;
    
  
     
     
     if(Double.parseDouble(ga) > 0){
     if(montoA > Double.parseDouble(ga)){
      montoA = montoA - Double.parseDouble(ga);
      if(montoA > 0)
      {
         // amortizap.setGastosa(String.valueOf(ga));     
          gami = 0;
      }
      else{
          gami = Double.parseDouble(ga) - montoA;      
      
      }
     }
     else{
     // sobregiro gastos administratvos
         gami = Double.parseDouble(ga) - montoA;      
         (ga) = String.valueOf(montoA);
         montoA = 0;
     }
     
     
     }
     
 
     
    
          
     if(Double.parseDouble(gm) > 0){
     if(montoA > Double.parseDouble(gm)){
     
     montoA = montoA - Double.parseDouble(gm);
         if(montoA > 0)
         {
        // amortizap.setMora(String.valueOf(gm));     
         gamo = 0;
         }
         else{
         gamo = Double.parseDouble(gm) - montoA;    
         }
     }
     
         else{
         gamo = Double.parseDouble(gm) - montoA;    
            // (ga) = String.valueOf(montoA);
             montoA = 0;
         
            
         }
      
         
     }
     
     
     
     
          if(Double.parseDouble(o1) > 0){
          if(montoA > Double.parseDouble(o1)){
          montoA = montoA - Double.parseDouble(o1);
              if(montoA > 0)
              {
          //    amortizap.setOtros(String.valueOf(o1));     
              otros1 = 0;
              }
              else{
              otros1 = Double.parseDouble(o1) - montoA;    
              }
          
          }
              else{
              otros1 = Double.parseDouble(o1) - montoA;    
                  montoA = 0;
              }
             
          }  
     
    
          if(Double.parseDouble(o2) > 0){
          if(montoA > Double.parseDouble(o2)){
          montoA = montoA - Double.parseDouble(o2);
              if(montoA > 0)
              {
            //  amortizap.setOtros2(String.valueOf(o2));     
              otros2 = 0;
              }
              else{
              otros2 = Double.parseDouble(o2) - montoA;    
              }
          
          }
              else{
              otros2 = Double.parseDouble(o2) - montoA;    
                  montoA = 0;
              }
              
             
          }  
    
      
  
    
        amortiza = montoA;
              
      
      mov.setGastosa(String.valueOf(ga));
          mov.setGastosm(String.valueOf(gm)); 
          mov.setOtros1(String.valueOf(o1));
      mov.setOtros2(String.valueOf(o2));
          if(ind == 0){
      mov.setMonto(String.valueOf(montoA));
          }
          if(ind == 1){       mov.setMonto(String.valueOf(j));
          } 
   
     if(Double.parseDouble(saldo) > amortiza){
  //   if(sobregiro < 0) {sobregiro = Math.abs(sobregiro);}
      if(sobregiro == 0) {
      
      // amortiza = new Double(co2.getAmortizacion());
      //  r=   new Double(co2.getResto()) ;
      r = new Double(saldo) -  new Double(co2.getAmortizacion());
      }
         if(sobregiro > 0) {
        //  amortiza = new Double(co2.getAmortizacion());
        //   r=   Double.parseDouble(saldo) - amortiza - sobregiro;
      //   amortiza = new Double(co2.getAmortizacion());
     //     r=   new Double(co2.getResto()) ;
      r = new Double(saldo) -  new Double(co2.getAmortizacion());
        r = r + sobregiro;
     if(r <  new Double(co2.getResto())) {
    
         amortizap.setAportacion(String.valueOf("0"));
         getCreditos().actualizarSobregiro(amortizap);
     }
     else {
              double t = new Double(co2.getResto()) - r;
              r=   new Double(co2.getResto()) ;
              sobregiro = -t;
              amortizap.setAportacion(String.valueOf(sobregiro));
              getCreditos().actualizarSobregiro(amortizap);
          }
    }
       
         if(sobregiro < 0) {
         //  amortiza = new Double(co2.getAmortizacion());
          sobregiro = -sobregiro;
          r = (new Double(saldo) -  new Double(co2.getAmortizacion())) - sobregiro;
        //    r=   new Double(co2.getResto()) + sobregiro;
         //   amortiza = new Double(co2.getAmortizacion());
       //   r=   new Double(co2.getResto()) ;
         }  
         
      
     amortizap.setSaldo(String.valueOf(r));
     }
     amortizap.setFechaUP(getFecha( crearform.getFechaVen()));
     
    double t = 0;
    
//    if(sobregiro > 0){
//     t = r +sobregiro;
//     if(t >= new Double(1000) )
//     gamo = 10.0;
//     
//     else{
//     gamo = 5.0;
//     }
//    }
     amortizap.setGastosa(String.valueOf(gami)); 
     
     
          amortizap.setMora(String.valueOf(gamo)); 
          amortizap.setOtros(String.valueOf(otros1)); 
          amortizap.setOtros2(String.valueOf(otros2)); 

      if(ind == 0){
      getCreditos().actualizarSaldo4(amortizap);
      
              AportacionBean aportacion =
                        obtenerDescuento(String.valueOf(new Long(crearform.getSocio())) , bean.getCondicion() ,getFechaDescuento(crearform.getFechaVen()) );
                         if(aportacion !=null) {     
                         aportacion.setFecha(getFechaDescuento(crearform.getFechaVen()));
                             grabarDescuentoCaja(aportacion  ,  String.valueOf(amortiza) ,ga  ,gm, producto)   ;  
                         }
      
          }
      }
          
      
            if(crearform.getOperacion().equals("1")  ){
            mov.setObs2(obs2);
            String ano = crearform.getAno();
            String fec = crearform.getPeriodo();
                String ano2 = crearform.getAno2();
                String fec2 = crearform.getPeriodo2();
                String dia = "01";    
            int p = new Integer(fec);
            int p2 = new Integer(fec2);
            double aporta = 0;
               if(bean!=null){
                    int de=      getCreditos().getAportacionFinal2((crearform.getSocio().trim()));
                      
                      int todiato = 0;
                      
                      todiato = new Integer(bean.getAportacion()) + de;
                
                
                aporta = new Double(todiato);
                   
                }
                
             int d = p2 -p;
             String g = String.valueOf(d);
             System.out.println("tam"+g);
            
            
            if(d == 0){
                String mes = String.valueOf(p);
                if(mes.length()==1){
                mes = "0"+mes;
                }
                String fecha2 = dia+"/"+mes+"/"+ano;
                
                AportacionBean cro = new AportacionBean();
                cro.setSocio(crearform.getSocio().trim());
                
                cro.setFecha(getFecha(fecha2));
                
                double monto = Double.parseDouble(crearform.getMonto());
                
                double diferencia = aporta - monto;
                
                
                cro.setAportacion(String.valueOf(aporta));
                
                cro.setAportacionP(String.valueOf(aporta));
                cro.setAportacionD(String.valueOf(monto));
                
                cro.setSobregiro(String.valueOf(diferencia));
                
       // estado=      getCreditos().validaAportes(crearform.getSocio(),getFecha(fecha2))  ;   
       //     if(estado == 0){
            getCreditos().registrarAportacion(cro);
         //       }
                
                obs = "Por el (los) Mes (es):"+ mes +"/"+ ano;
            }
            if(d >0)
            {
            for(p =p; p <= p2 ; p++)
            {
             String mes = String.valueOf(p);
             if(mes.length()==1){
             mes = "0"+mes;
             }
             String fecha2 = dia+"/"+mes+"/"+ano;
            
            AportacionBean cro = new AportacionBean();
            cro.setSocio(crearform.getSocio());           
            
            cro.setFecha(getFecha(fecha2));
                double monto = Double.parseDouble(crearform.getMonto());
                double m = monto / (d +1);
                
                
                double diferencia = aporta - m;
                
                
                cro.setAportacion(String.valueOf(aporta ));
                
                cro.setAportacionP(String.valueOf(aporta ));
                cro.setAportacionD(String.valueOf(m));
                
                cro.setSobregiro(String.valueOf(diferencia));
                
            //    estado=      getCreditos().validaAportes(crearform.getSocio(),getFecha(fecha2))  ;
           //     if(estado == 0){
            getCreditos().registrarAportacion(cro);
            //    }
            obs = "Por el (los) Mes (es):"+ fec+"/"+ano+ " a "+fec2+"/"+ano;
            
            }
            }
            if(estado == 0)      {
       
                }
            
            mov.setMontoAportacion(crearform.getApecope());
            
            }
            
         
            if(crearform.getOperacion().equals("9")  ){
            mov.setObs2(obs2);
            String ano = crearform.getAno();
            String fec = crearform.getPeriodo();
                String ano2 = crearform.getAno2();
                String fec2 = crearform.getPeriodo2();
                String dia = "01";    
            int p = new Integer(fec);
            int p2 = new Integer(fec2);
            double aporta = 0;
               if(bean!=null){
                    int de=     new Integer( bean.getAporteCope());
                    
                      int todiato = 0;
                      
                      todiato = new Integer(de);
                
                
               // aporta = new Double(todiato);
              aporta = Double.parseDouble(crearform.getApeno());     
                }
                
             int d = p2 -p;
             String g = String.valueOf(d);
             System.out.println("tam"+g);
            
            
            if(d == 0){
                String mes = String.valueOf(p);
                if(mes.length()==1){
                mes = "0"+mes;
                }
                String fecha2 = dia+"/"+mes+"/"+ano;
                
                AportacionBean cro = new AportacionBean();
                cro.setSocio(crearform.getSocio().trim());
                
                cro.setFecha(getFecha(fecha2));
                
                double monto = Double.parseDouble(crearform.getMonto());
                
                double diferencia = aporta - monto;
                
                
                
                cro.setAportacion(String.valueOf(aporta));
                cro.setAportacionP(String.valueOf(aporta));
                cro.setAportacionD(String.valueOf(monto));
                
                cro.setSobregiro(String.valueOf(diferencia));
                
                
                getCreditos().registrarAportacionCope(cro);
                
                
                obs = "Por el (los) Mes (es):"+ mes +"/"+ ano;
            }
            if(d >0)
            {
            for(p =p; p <= p2 ; p++)
            {
             String mes = String.valueOf(p);
             if(mes.length()==1){
             mes = "0"+mes;
             }
             String fecha2 = dia+"/"+mes+"/"+ano;
            
            AportacionBean cro = new AportacionBean();
            cro.setSocio(crearform.getSocio());           
            
            cro.setFecha(getFecha(fecha2));
                double monto = Double.parseDouble(crearform.getMonto());
               double m = monto / (d +1);
             //   double m = monto;
                
                double diferencia = aporta - monto;
                
                
                cro.setAportacion(String.valueOf(m ));
                
                cro.setAportacionP(String.valueOf(m ));
                cro.setAportacionD(String.valueOf(m));
                
                cro.setSobregiro(String.valueOf(diferencia));
            getCreditos().registrarAportacionCope(cro);
            obs = "Por el (los) Mes (es):"+ fec+"/"+ano+ " a "+fec2+"/"+ano;
            
            }
            }
            mov.setMontoAportacion(crearform.getMonto());
            
            }
            
           if(crearform.getOperacion().equals("3")){
           
           PrestamoBean b = getCreditos().getPrestamo(new Integer(crearform.getChk()));
           
           if(b!=null){
           mov.setMonto( b.getImporte());
           mov.setGastosa(b.getGastosTramite());
               obs = crearform.getObs();
               mov.setObs2(obs);
           }
                producto = new Integer(b.getProducto());    
           
           getCreditos().desembolsarPrestamo(new Integer(crearform.getChk()));
           
           if(b.getProducto().equals("6")){
           getCreditos().tieneCompraDeuda(new Integer(persona));
           }
                       
                ChequeBean bean2 = new ChequeBean();
                bean2.setNombre(crearform.getNombres());
                bean2.setMonto(crearform.getMonto());
                bean2.setCheque(crearform.getCheque());
                System.out.println(crearform.getMonto());
                bean2.setMontoLetras(getML(crearform.getMonto()));
                bean2.setMotivo("PRESTAMO");
                bean2.setCentro(bean.getCentro());
                bean2.setCondicion(bean.getCondicion());
                int id =     getCreditos().registrarCheque(bean2);
            
            
            }
      
            if(crearform.getOperacion().equals("6")){
            double amor = Double.parseDouble(crearform.getAmortiza());
            double sobre =  Double.parseDouble(crearform.getSobregiro());
            double t = amor + sobre;
           mov.setMonto(String.valueOf(t));
           mov.setGastosa(crearform.getGastosAdm());
           mov.setGastosm(crearform.getGastosMora());
           mov.setOtros1(crearform.getOtros1());            
            mov.setOtros2(crearform.getOtros2());
            mov.setObs2(obs2);
            
                PrestamoBean tata = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                if(tata!=null) {    
                    producto = new Integer(tata.getProducto());  
                    if(tata.getProducto().equals("6")) {
          
                        getCreditos().cancelaCompraDeuda(new Integer(persona));
                    }
                }
                
                AportacionBean aportacion =
                                       obtenerDescuento2(String.valueOf(new Long(crearform.getSocio())) , bean.getCondicion() ) ;
                                        if(aportacion !=null) {    
                                            aportacion.setInteresP("0.0");
                                            aportacion.setMoraP("0.0");
                                            grabarDescuentoCaja(aportacion  ,  String.valueOf(t) , "0.0"  , "0.0", producto)   ;  
                                        }
                
            }
           
            if(crearform.getOperacion().equals("14")){
                   
                 String[] parts = crearform.getPalabra().split(",");
                
                for(int i = 0; i <  parts.length ; i++){
                 
                String chk = parts[i];
               
                int raya = chk.indexOf("-");
                 String soc = chk.substring(0,raya);
                 int pre = chk.indexOf("?");
                 int pre2 = chk.indexOf("_");
                 int pre3 = chk.indexOf("|");
                 int pre4 = chk.indexOf("#");
                
                String perido = chk.substring(raya+1, pre);
                String apop = chk.substring(pre+1,pre2);
                String apod = chk.substring(pre2+1,pre3);
                String d = chk.substring(pre3+1,pre4);
                String fp = chk.substring(pre4+1);
               
               
                AportacionBean aportacion = new  AportacionBean();
                
                double p1 = 0;
                p1 = Double.parseDouble(d) - Double.parseDouble(apod);
                    //Cambio
                      if (p1 < 0) {
                        p1 = ( Double.parseDouble(apod) ) + Double.parseDouble(crearform.getMonto());
                      }
                      //Fin Cambio               
                aportacion.setAportacionP("0");
                aportacion.setAportacionD(String.valueOf( p1));
                aportacion.setAportacion("0");
                aportacion.setSocio(soc);
                aportacion.setFecha(getFecha("01/"+perido));
                aportacion.setFechaPago(getFecha(fp));
                getCreditos().registrarAportacion(aportacion);
                 
           
                }
                
                obs = crearform.getObs();
                mov.setObs2(obs);
                mov.setMonto(crearform.getMonto());
            }
            
            
            if(crearform.getOperacion().equals("15")){
            
                String[] parts = crearform.getPalabra().split(",");
                
                for(int i = 0; i <  parts.length ; i++){
                
                String chk = parts[i];
                
                int raya = chk.indexOf("-");
                String soc = chk.substring(0,raya);
                int pre = chk.indexOf("?");
                int pre2 = chk.indexOf("_");
                int pre3 = chk.indexOf("|");
                int pre4 = chk.indexOf("#");
                
                String perido = chk.substring(raya+1, pre);
                String apop = chk.substring(pre+1,pre2);
                String apod = chk.substring(pre2+1,pre3);
                String d = chk.substring(pre3+1,pre4);
                String fp = chk.substring(pre4+1);
                
                
                AportacionBean aportacion = new  AportacionBean();
                
                double p1 = 0;
                p1 = Double.parseDouble(d) - Double.parseDouble(apod);
                    //Cambio
                      if (p1 < 0) {
                        p1 = ( Double.parseDouble(apod) ) + Double.parseDouble(crearform.getMonto());
                      }
                      //Fin Cambio             
                aportacion.setAportacionP("0");
                aportacion.setAportacionD(String.valueOf( p1));
                aportacion.setAportacion("0");
                aportacion.setSocio(soc);
                aportacion.setFecha(getFecha("01/"+perido));
                aportacion.setFechaPago(getFecha(fp));
                getCreditos().registrarAportacionCope(aportacion);
                
                
                }
                
                obs = crearform.getObs();
                mov.setObs2(obs);
                mov.setMonto(crearform.getMonto());
            
            }
            
            
            if(crearform.getOperacion().equals("10")){
                String ga =     crearform.getGastosAdm();
                String gm =     crearform.getGastosMora();
                String o1 = crearform.getOtros1();
                String o2 = crearform.getOtros2();
                
                double amortiza = 0;     
                double gami  = 0;
                double gamo = 0;
                double otros1 = 0;
                double otros2 = 0;
                double gP = 0;
                
                
                amortiza = Double.parseDouble(crearform.getDescuenta());      
            
            double sob = 0;
            sob = Double.parseDouble(crearform.getSobregiro());
            double mon = 0;
            mon = Double.parseDouble(crearform.getMonto());
          
               
           if(mon > 0 )    
               {
                    montoA = mon;
                if(Double.parseDouble(ga) > 0){
                if(montoA > Double.parseDouble(ga)){
                 montoA = montoA - Double.parseDouble(ga);
                 if(montoA > 0)
                 {
                    // amortizap.setGastosa(String.valueOf(ga));     
                     gami = 0;
                 }
                 else{
                     //gami = Double.parseDouble(ga) - montoA;      
                     //Si 11-06-2016 se agrago esta linea
                     gami = 0;
                 }
                }
                else{
                // sobregiro gastos administratvos
                    gami = Double.parseDouble(ga) - montoA;      
                    (ga) = String.valueOf(montoA);
                    montoA = 0;
                }
                
                
                }
                
                
                
                
                     
                if(Double.parseDouble(gm) > 0){
                if(montoA > Double.parseDouble(gm)){
                
                montoA = montoA - Double.parseDouble(gm);
                    if(montoA > 0)
                    {
                   // amortizap.setMora(String.valueOf(gm));     
                    gamo = 0;
                    }
                    else{
                    gamo = Double.parseDouble(gm) - montoA;    
                    }
                }
                
                    else{
                    gamo = Double.parseDouble(gm) - montoA;    
                       // (ga) = String.valueOf(montoA);
                        montoA = 0;
                    
                       
                    }
                 
                    
                }
                
                
                
                
                     if(Double.parseDouble(o1) > 0){
                     if(montoA > Double.parseDouble(o1)){
                     montoA = montoA - Double.parseDouble(o1);
                         if(montoA > 0)
                         {
                     //    amortizap.setOtros(String.valueOf(o1));     
                         otros1 = 0;
                         }
                         else{
                         otros1 = Double.parseDouble(o1) - montoA;    
                         }
                     
                     }
                         else{
                         otros1 = Double.parseDouble(o1) - montoA;    
                             montoA = 0;
                         }
                        
                     }  
                
                
                     if(Double.parseDouble(o2) > 0){
                     if(montoA > Double.parseDouble(o2)){
                     montoA = montoA - Double.parseDouble(o2);
                         if(montoA > 0)
                         {
                       //  amortizap.setOtros2(String.valueOf(o2));     
                         otros2 = 0;
                         }
                         else{
                         otros2 = Double.parseDouble(o2) - montoA;    
                         }
                     
                     }
                         else{
                         otros2 = Double.parseDouble(o2) - montoA;    
                             montoA = 0;
                         }
                         
                        
                     }  
                  
                  
                    PrestamoBean b2 = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                  double g = 0;
                  double m = 0;
                    if(b2!=null){
                        producto = new Integer(b2.getProducto());    
                        g = Double.parseDouble(b2.getGastosa());
                        if(g> 0)
                        {
                        gami = g - Double.parseDouble(ga);
                        } 
                        m = Double.parseDouble(b2.getMora());
                        if(m> 0)
                        {
                        gamo = m - Double.parseDouble(gm);
                        }  
                    }  
                  
               
                    PrestamoBean amortizap = new PrestamoBean ();
                
                    amortizap.setGastosa(String.valueOf(gami)); 
                    
                    amortizap.setCodigo(crearform.getChk());
                         amortizap.setMora(String.valueOf(gamo)); 
                         amortizap.setOtros(String.valueOf(otros1)); 
                         amortizap.setOtros2(String.valueOf(otros2));
                   getCreditos(). actualizarSaldo01(amortizap);
                
                            
                    mon = montoA;
               double t = 0;
                    if(mon >= 0 )
                                  {//t = mon - sob;
                               
                                   PrestamoBean b = getCreditos().getPrestamo(new Integer(crearform.getChk()));
                                   
                                   if(b!=null){
                                       b.setCodigo(crearform.getChk());
                                       producto = new Integer(b.getProducto());    
                                   
                                       double so = 0;
                                       String so1 = b.getAportacion();
                                       if(so1!=null){
                                       so = Double.parseDouble(so1);
                                       }
                                       if(sob>0) {
                                        t = so - sob;
                                        if(t < 0) t = 0;
                                           b.setAportacion(String.valueOf( t));
                                           
                                           getCreditos().actualizarSobregiro(b);
                                       }
                         
                                   
                                       mov.setMonto(String.valueOf(sob));
                                   
                                       if(amortiza > 0) {
                                       double saldroPres = Double.parseDouble( b.getSaldo());
                                       double resto = saldroPres - amortiza;
                                         amortizap.setSaldo(String.valueOf(resto));  
                                       getCreditos().actualizarSaldoSolo(amortizap);
                                       }
                                   
                                   
                                   }
                               
                                   }
   
                }
                mov.setObs2(obs2);
                //mvo.setMonto(crearform.getMonto
                mov.setMonto(String.valueOf(crearform.getSobregiro()));
                     mov.setGastosa(String.valueOf(ga));
                     mov.setAbonoPrestamo(String.valueOf(crearform.getDescuenta()));
                         mov.setGastosm(String.valueOf(gm)); 
                         mov.setOtros1(String.valueOf(o1));
                     mov.setOtros2(String.valueOf(o2));
            
              
            
            }
            
            if(crearform.getOperacion().equals("7")){
                obs = crearform.getObs();
                mov.setObs2(obs);
                mov.setDevolucion(crearform.getDevaporte());
                mov.setMontoIngreso(crearform.getDeuda());
                
            ChequeBean bean3 = new ChequeBean();
            bean3.setNombre(crearform.getNombres());
            bean3.setMonto(crearform.getMonto());
            bean3.setCheque(crearform.getCheque());
            System.out.println(crearform.getMonto());
            bean3.setMontoLetras(getML(crearform.getMonto()));
                bean3.setMotivo("BENEFICIO");
                bean3.setCentro(bean.getCentro());
                bean3.setCondicion(bean.getCondicion());
                
                BeneficiosBean j = new BeneficiosBean();
                j.setId(crearform.getChk());
                j.setTipo("DESEMBOLSADO");
                
                getCreditos().actualizarEstadoBeneficio(j);
            int id =     getCreditos().registrarCheque(bean3);
            }
            
            
            
            
            
      
      System.out.println(estado);
      
      
  mov.setPrestamo(crearform.getChk());
      mov.setSocio(crearform.getSocio());
      mov.setOperacion(crearform.getOperacion());
    
      mov.setId(crearform.getChk());
      mov.setCheque(crearform.getCheque());
      mov.setObs(obs);
      mov.setFormaPago(crearform.getFormaPago());
    mov.setCodigoContable(crearform.getCodigoContable());
      mov.setFecha(getFechaActual());
      
      
   HttpSession ses = request.getSession();
   String user = (String)ses.getAttribute("usuario");
   mov.setUsuario(user);
      
            int c =  0;
   if(estado == 0)   {
  c =   getCreditos().registrarMovimiento(mov);
          
            }
            if(crearform.getOperacion().equals("3")){ 
            
            getCreditos().desembolsarPrestamo2(new Integer(crearform.getChk()), c);
            }
            
            if(c!=0){
             getCreditos().actualizaMovimientoProducto(new Long(c),0);  
             JasperReport jr = null;
             InputStream reportStream = null;
             
             String rutaJasper = getServlet().getServletConfig().getServletContext().getInitParameter("rutajasper");
             System.out.println("ruta:"+rutaJasper);
            
          
            if(crearform.getOperacion().equals("1")){
                               reportStream = getServlet().getServletConfig().getServletContext()
                               .getResourceAsStream(rutaJasper + "/aportacion.jasper");
            }
            
            if(crearform.getOperacion().equals("9")){
                               reportStream = getServlet().getServletConfig().getServletContext()
                               .getResourceAsStream(rutaJasper + "/aportacion2.jasper");
            }
            
                if(crearform.getOperacion().equals("14")){
                                   reportStream = getServlet().getServletConfig().getServletContext()
                                   .getResourceAsStream(rutaJasper + "/sobregiro_fonbes.jasper");
                }
                
                if(crearform.getOperacion().equals("15")){
                                   reportStream = getServlet().getServletConfig().getServletContext()
                                   .getResourceAsStream(rutaJasper + "/sobregiro_coopam.jasper");
                }
            
                if(crearform.getOperacion().equals("12")){
                                   reportStream = getServlet().getServletConfig().getServletContext()
                                   .getResourceAsStream(rutaJasper + "/rifa.jasper");
                }
                
                if(crearform.getOperacion().equals("11")){
                                   reportStream = getServlet().getServletConfig().getServletContext()
                                   .getResourceAsStream(rutaJasper + "/multa.jasper");
                }
            
            if(crearform.getOperacion().equals("2")){
                reportStream = getServlet().getServletConfig().getServletContext()
                .getResourceAsStream(rutaJasper + "/amortizaaee.jasper");      
            }       
            
                if(crearform.getOperacion().equals("10")){
                    reportStream = getServlet().getServletConfig().getServletContext()
                    .getResourceAsStream(rutaJasper + "/sobregiroaee.jasper");      
                }      
                
                if(crearform.getOperacion().equals("4")){
                    reportStream = getServlet().getServletConfig().getServletContext()
                    .getResourceAsStream(rutaJasper + "/devofonbes.jasper");      
                }      
                
                if(crearform.getOperacion().equals("5")){
                    reportStream = getServlet().getServletConfig().getServletContext()
                    .getResourceAsStream(rutaJasper + "/devocoopam.jasper");      
                }      
            
            if(crearform.getOperacion().equals("6")){
                reportStream = getServlet().getServletConfig().getServletContext()
                .getResourceAsStream(rutaJasper + "/cancelacionT.jasper");      
            } 
            
                if(crearform.getOperacion().equals("13")){
                    reportStream = getServlet().getServletConfig().getServletContext()
                    .getResourceAsStream(rutaJasper + "/devolucion.jasper");      
                } 
                
                if(crearform.getOperacion().equals("16")){
                    reportStream = getServlet().getServletConfig().getServletContext()
                    .getResourceAsStream(rutaJasper + "/ingresos.jasper");      
                } 
            
            if(crearform.getOperacion().equals("3")){
                reportStream = getServlet().getServletConfig().getServletContext()
                .getResourceAsStream(rutaJasper + "/reciboAEE.jasper");      
            } 
            
            if(crearform.getOperacion().equals("7")){
                reportStream = getServlet().getServletConfig().getServletContext()
                .getResourceAsStream(rutaJasper + "/beneficios.jasper");      
            }  
            
             jr = JasperManager.loadReport(reportStream);

             //Establecer los parametros
             Map parameters = new HashMap();
             
            if(crearform.getOperacion().equals("1")){
            
                        parameters.put("id", new Integer(c));
                        crearform.setAportacion("");
            }
            
                if(crearform.getOperacion().equals("14")){
                
                            parameters.put("id", new Integer(c));
                         //   crearform.setAportacion("");
                }
                
                          if(crearform.getOperacion().equals("15")){
                          
                                      parameters.put("id", new Integer(c));
                                   //   crearform.setAportacion("");
                          }
                
            if(crearform.getOperacion().equals("9")){
            
                        parameters.put("id", new Integer(c));
                        String h = getCreditos().getAportacionTotalCo(new Integer(crearform.getSocio()));
                parameters.put("total", h);
                        crearform.setAportacion("");
            }
            if(crearform.getOperacion().equals("7")){
            
                        parameters.put("id", new Integer(c));
            }   
                if(crearform.getOperacion().equals("11")){
                
                            parameters.put("id", new Integer(c));
                }   
                
                if(crearform.getOperacion().equals("12")){
                
                            parameters.put("id", new Integer(c));
                } 
                
                if(crearform.getOperacion().equals("13")){
                
                            parameters.put("id", new Integer(c));
                } 
                
                
                if(crearform.getOperacion().equals("16")){
                
                            parameters.put("id", new Integer(c));
                } 
                
                
            if(crearform.getOperacion().equals("2")){
                parameters.put("id", new Integer(c));
                parameters.put("saldo", String.valueOf(r));
              //  parameters.put("prestamo", new Integer(crearform.getChk()));
              //  parameters.put("cuota", new Integer(crearform.getChk2()));
            }
            
               if(crearform.getOperacion().equals("10")){
                   parameters.put("id", new Integer(c));
                 //  parameters.put("prestamo", new Integer(crearform.getChk()));
                 //  parameters.put("cuota", new Integer(crearform.getChk2()));
               }
            
            if(crearform.getOperacion().equals("6")){
            
                parameters.put("id", new Integer(c));
            }
            
                if(crearform.getOperacion().equals("4")){
                
                    parameters.put("id", new Integer(c));
                }
                
                if(crearform.getOperacion().equals("5")){
                
                    parameters.put("id", new Integer(c));
                }
            
            if(crearform.getOperacion().equals("3")){
            
                parameters.put("numero", new Integer(c));
            }
                          
             ServletContext servletContext = getServlet().getServletConfig().getServletContext();
             WebApplicationContext wac =
             WebApplicationContextUtils.getRequiredWebApplicationContext(servletContext);
             DataSource ds =   (DataSource)wac.getBean("edataSource"); 

             Connection conn  = ds.getConnection();
             
             if(conn!=null)  {    
//                 PrintService service = null;
//                 PrintRequestAttributeSet aset = new HashPrintRequestAttributeSet();
//                 aset.add(MediaSizeName.ISO_A4);
//
//                 //list all printers and select the one selected by the user
//                 PrintService[] services3 = PrintServiceLookup.lookupPrintServices(null, null);
//                 
//                 for(int k=0;k<services3.length;k++){
//                 if(services3[k].getName().trim().equals("HP LaserJet"))
//                 {
//                 service = services3[k];
//                 }
//                 }
//                 if(service == null){
//                 System.out.println("PRINTER NOT FOUND->");
//                 }else{
//                 
//                     JasperPrint jasperPrint = (JasperPrint) JRLoader.loadObject(reportStream);
//                     JRExporter exporter = new JRPrintServiceExporter();
//                     exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
//                     exporter.setParameter(JRPrintServiceExporterParameter.PRINT_SERVICE_ATTRIBUTE_SET, service.getAttributes());
//                     exporter.setParameter(JRPrintServiceExporterParameter.DISPLAY_PAGE_DIALOG, Boolean.FALSE);
//                     exporter.setParameter(JRPrintServiceExporterParameter.DISPLAY_PRINT_DIALOG, Boolean.FALSE);
//                     exporter.exportReport();
//
//                 
//                 }
             
             
             
             
 byte[] bytes = JasperRunManager.runReportToPdf(jr, parameters,conn);
 //       byte[] bytes = JasperRunManager.runReportToPdf(jr, parameters,conn);
  response.setContentType("application/pdf");
  response.setContentLength(bytes.length);
  ServletOutputStream ouputStream = response.getOutputStream();
  ouputStream.write(bytes, 0, bytes.length);
  ouputStream.flush();
  ouputStream.close();
                 
                 if(crearform.getOperacion().equals("6")){
            getCreditos().cancelarCuotas(new Integer(crearform.getChk()));
            getCreditos().cancelarPrestamo(new Integer(crearform.getChk()));
            
                 }
                 
                 if(crearform.getOperacion().equals("7")){
                 getCreditos().actualizarBeneficios(new Integer(crearform.getChk()));
                 }        
              //   if(crearform.getOperacion().equals("7")){
              //   getCreditos().cancelarCuotas(new Integer(crearform.getChk()));
             //    getCreditos().cancelarPrestamo(new Integer(crearform.getChk()));
                 
             //    }
                 
                 
             }
        
        crearform.setMonto("");
        crearform.setOperacion("");
        crearform.setSocio("");
                crearform.setChk("");
            }
            
                
            
            else {  
   
                accionMapa = 0;
              
       
          
            } 
    //   }
     //  else{
       //    accionMapa = 19;
      // }
           
        } catch (Exception e) {
        e.printStackTrace();
        accionMapa = 0;
        }
        return accionMapa;
        }
        
    public String getFecha(String fecha){
    String nuevaFecha = null;
    try{
    int puto = fecha.indexOf("/");
    int puto2 = fecha.lastIndexOf("/");
    String dia = fecha.substring(0,puto);
    String mes = fecha.substring(puto+1,puto2);
    String ano = fecha.substring(puto2+1);
    nuevaFecha = ano + "-" +mes+ "-" + dia;
    
    }catch(Exception e){
    e.printStackTrace();
    }
    return nuevaFecha;
    }
    
    public String getFechaDescuento(String fecha){
    String nuevaFecha = null;
    try{
    int puto = fecha.indexOf("/");
    int puto2 = fecha.lastIndexOf("/");
    String dia = fecha.substring(0,puto);
    String mes = fecha.substring(puto+1,puto2);
    String ano = fecha.substring(puto2+1);
    nuevaFecha = ano + "-" +mes+ "-" + "01";
    
    }catch(Exception e){
    e.printStackTrace();
    }
    return nuevaFecha;
    }
    
    private boolean validaPeriodo(String fecha, String fecha2){
    boolean flag = false;
        int puto = fecha.indexOf("/");
        int puto2 = fecha.lastIndexOf("/");
        String dia = fecha.substring(0,puto);
        String mes = fecha.substring(puto+1,puto2);
        String ano = fecha.substring(puto2+1);
        
        int puto3 = fecha2.indexOf("/");
        int puto4 = fecha2.lastIndexOf("/");
        String dia2 = fecha2.substring(0,puto3);
        String mes2 = fecha2.substring(puto+1,puto4);
        String ano2 = fecha2.substring(puto4+1);
        if(mes.equals(mes2)&& ano.equals(ano2)) flag = true;
        
    return flag;
    }

    
    public String getFechaActual() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        String f = sdf.format(new java.util.Date());
        return f;
    }
    
    public int getDia() {
        SimpleDateFormat sdf = new SimpleDateFormat("dd");
        String f = sdf.format(new java.util.Date());
        return Integer.parseInt(f);
    }
    
    private void grabarDescuentoCaja(AportacionBean existente,String amortiza, String gastosa, String gastosmo ,Integer producto){
          DecimalFormatSymbols dfs = new DecimalFormatSymbols();
          dfs.setDecimalSeparator('.'); 
          dfs.setGroupingSeparator(',');
          DecimalFormat df1 = new DecimalFormat("#0.00",dfs); 
          df1.setGroupingSize(3);
        MantenimientoDAO dao = new MantenimientoDAO();
        AportacionBean aportacion1 = new AportacionBean();
        String amortizacion = amortiza;
        String gastosMo = gastosmo;
        String gastosAdm = gastosa;
        Double amortizaDesc = 0.0;
        Double gastosDesc = 0.0;
        Double moraDesc = 0.0;
        Double tamortizaDesc = 0.0;
        Double tgastosDesc = 0.0;
        Double tmoraDesc = 0.0;
        
        aportacion1.setSocio(String.valueOf(new Long(existente.getSocio())));
        aportacion1.setFecha(existente.getFecha());
        aportacion1.setProducto(String.valueOf(producto));
          if(existente.getProducto()!=null ){ if( existente.getProducto().equals(String.valueOf(producto))) {
            amortizaDesc = Double.parseDouble(existente.getAmortizacion());
            gastosDesc = Double.parseDouble(existente.getInteresP());
            moraDesc = Double.parseDouble(existente.getMoraP());
            if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
            tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
            tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
            tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
            aportacion1.setAmortizacion(String.valueOf(df1.format(tamortizaDesc)));        
            aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
            aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
            dao.actualizaDescuentoProducto1Caja(aportacion1);
            }
            }}
        
          if(existente.getProducto2()!=null ){ if(existente.getProducto2().equals(String.valueOf(producto))) {
              amortizaDesc = Double.parseDouble(existente.getAmortizacionp());
              gastosDesc = Double.parseDouble(existente.getInterespp());
              moraDesc = Double.parseDouble(existente.getMorapp());
              if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
              tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
              tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
              tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
              aportacion1.setAmortizacion(String.valueOf(df1.format(tamortizaDesc)));        
              aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
              aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
              dao.actualizaDescuentoProducto2Caja(aportacion1);
              }
          }}
          
          if(existente.getProducto3()!=null ){ if( existente.getProducto3().equals(String.valueOf(producto))) {
              amortizaDesc = Double.parseDouble(existente.getAmortizacionp2());
              gastosDesc = Double.parseDouble(existente.getInterespp2());
              moraDesc = Double.parseDouble(existente.getMorapp2());
              if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
              tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
              tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
              tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
              aportacion1.setAmortizacion(String.valueOf(df1.format(tamortizaDesc)));        
              aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
              aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
              dao.actualizaDescuentoProducto3Caja(aportacion1);
              }
          }}
          
          if(existente.getProducto4()!=null ){ if( existente.getProducto4().equals(String.valueOf(producto))) {
              amortizaDesc = Double.parseDouble(existente.getAmortizacionp3());
              gastosDesc = Double.parseDouble(existente.getInterespp3());
              moraDesc = Double.parseDouble(existente.getMorapp3());
              if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
              tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
              tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
              tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
              aportacion1.setAmortizacion(String.valueOf(tamortizaDesc));        
              aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
              aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
              dao.actualizaDescuentoProducto4Caja(aportacion1);
              }
          }}
          
           if(existente.getProducto5()!=null ){ if( existente.getProducto5().equals(String.valueOf(producto))) {
              amortizaDesc = Double.parseDouble(existente.getAmortizacionp4());
              gastosDesc = Double.parseDouble(existente.getInterespp4());
              moraDesc = Double.parseDouble(existente.getMorapp4());
              if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
              tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
              tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
              tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
              aportacion1.setAmortizacion(String.valueOf(df1.format(tamortizaDesc)));        
              aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
              aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
              dao.actualizaDescuentoProducto5Caja(aportacion1);
              }
          }}
          
           if(existente.getProducto6()!=null ){ if( existente.getProducto6().equals(String.valueOf(producto))) {
              amortizaDesc = Double.parseDouble(existente.getAmortizacionp5());
              gastosDesc = Double.parseDouble(existente.getInterespp5());
              moraDesc = Double.parseDouble(existente.getMorapp5());
              if(amortizaDesc > 0 || gastosDesc > 0 || moraDesc > 0) {
              tamortizaDesc = amortizaDesc - Double.parseDouble(amortizacion);  
              tgastosDesc = gastosDesc - Double.parseDouble(gastosAdm); 
              tmoraDesc =  moraDesc - Double.parseDouble(gastosMo); 
              aportacion1.setAmortizacion(String.valueOf(df1.format(tamortizaDesc)));        
              aportacion1.setInteresP(String.valueOf(df1.format(tgastosDesc)));
              aportacion1.setMoraP(String.valueOf(df1.format(tmoraDesc)));
              dao.actualizaDescuentoProducto6Caja(aportacion1);
              }
          }}
        
        
      }
    
    private void actualizaPlanilla(String socio , Integer producto, String periodo, String amortiza, String gastosa, String gastosmo ) {
        MantenimientoDAO dao = new MantenimientoDAO();
        AportacionBean aportacion = new AportacionBean();
        aportacion.setSocio(socio);
        aportacion.setProducto(String.valueOf(producto));
                                     aportacion.setFecha(periodo);
                                     aportacion.setAmortizacion(amortiza);
                                     aportacion.setInteresP(gastosa);
                                     aportacion.setMoraP(gastosmo);
        dao.actualizaDescuentoProducto1Multiple4(aportacion);
    }
    
    private AportacionBean  obtenerDescuento(String socio , String condicion, String periodo ) {
             AportacionBean aportacion = null;
              String codigo = "";
             String sobre = "000";
              Util helper = Util.getInstance();
              StringBuffer data2 = new StringBuffer("");
              data2.append(helper.ConvertNumericAString(socio, 7));
              if (
                  condicion.equals("PENSIONISTA")) {


                  if (socio.length() > 7) {
                    codigo = (socio);
                  } else {
                      codigo = (data2.toString() + sobre);
                  }


              } else {
                  codigo = (data2.toString());
              }
              
             aportacion = getCreditos().getDescuentoCaja(String.valueOf(new Long(codigo)), periodo );
             if(aportacion!=null) {
                 aportacion.setSocio(codigo);
             }
             return aportacion;
          }    
          
    private AportacionBean  obtenerDescuento2(String socio , String condicion ) {
             AportacionBean aportacion = null;
              String codigo = "";
             String sobre = "000";
              Util helper = Util.getInstance();
              StringBuffer data2 = new StringBuffer("");
              data2.append(helper.ConvertNumericAString(socio, 7));
              if (
                  condicion.equals("PENSIONISTA")) {


                  if (socio.length() > 7) {
                    codigo = (socio);
                  } else {
                      codigo = (data2.toString() + sobre);
                  }


              } else {
                  codigo = (data2.toString());
              }
              
             aportacion = getCreditos().getDescuentoCaja2(String.valueOf(new Long(codigo)) );
             if(aportacion!=null) {
                 aportacion.setSocio(codigo);
             }
             return aportacion;
          }   
    
    String getML(String mo){
    
        DecimalFormatSymbols dfs = new DecimalFormatSymbols();          
        dfs.setDecimalSeparator('.'); 
        dfs.setGroupingSeparator(',');
        DecimalFormat df1 = new DecimalFormat("###,###,##0.00",dfs); 
        df1.setGroupingSize(3); 
        
        
            n2t conv=new n2t();
            
            
        double m =  Double.parseDouble(mo);
         
            
           System.out.println(String.valueOf(m)); 
            
           String montoLetras; 
           String montol = df1.format(m);
            int p = montol.indexOf(".");
         
            String v = montol.substring(p+1);
            
         String m2 =   pasarDecimal(mo);
        int p2 = m2.indexOf(".");
        String pn = m2.substring(0,p2);
        
        System.out.println(pn); 
            
        montoLetras = conv.convertirLetras(Integer.parseInt(pn)).toUpperCase();      
        montoLetras = montoLetras+" " +v +"/100" ;
    
        return montoLetras;
    }
    
    String pasarDecimal(String decimal){
        double x = Double.parseDouble(decimal);
        DecimalFormatSymbols simbolos = new DecimalFormatSymbols();
        simbolos.setDecimalSeparator('.');
        DecimalFormat formateador = new DecimalFormat("#0.00", simbolos);
        return formateador.format(x);
    }
    
                 
}