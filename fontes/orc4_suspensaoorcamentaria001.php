<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2013  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta_plugin.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>    
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
    <form name="form1" method="post" onSubmit="return js_atualizarSuspensao();">
      <fieldset style="margin-top: 30px; ">
        <legend>
          <strong>
            Suspensão Orçamentária
          </strong>
        </legend>
        <table border="0" width="100%">
          <tr>
            <td>
              <div id='ctnGridSuspensao'></div>
            </td>
          </tr>
          <tr>
            <td align="center">
              <input type='button' name='btnSalvarSuspensao' value='Salvar' onclick='js_atualizarSuspensao()'/>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
    <?
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>
  </body>
</html>

<script>

window.onload = function(){
  js_criaGridSuspensao();
}

function js_criaGridSuspensao(){

  js_divCarregando('Carregando dados da suspensão...', 'msgBox');

  oGridSuspensao = new DBGrid('oGridSuspensao');
  oGridSuspensao.nameInstance = 'oGridSuspensao';
  oGridSuspensao.setCellWidth(new Array( '80px' ,
                                            '300px',
                                            '50px',
                                            '50px',
                                            '50px',
                                            '50px',
                                            '50px',
                                            '50px',
                                            '50px',
                                            '50px'
                                           ));
  oGridSuspensao.setCellAlign(new Array( 'center'  ,
                                            'left',
                                            'center',
                                            'center',
                                            'center',
                                            'center',
                                            'center',
                                            'center',
                                            'center',
                                            'center'
                                           ));
  oGridSuspensao.setHeader(new Array( 'Órgão',
                                         'Unidade',
                                         'Anexo',
                                         'Fonte Recurso',
                                         'Empenho',
                                         'Liquidação',
                                         'Pagamento',
                                         'Sequencial_Suspensao',
                                         'Exercicio',
                                         'Cod_Unidade'
                                        ));
  oGridSuspensao.setHeight(420);
  oGridSuspensao.aHeaders[7].lDisplayed = false;
  oGridSuspensao.aHeaders[8].lDisplayed = false;
  oGridSuspensao.aHeaders[9].lDisplayed = false;
  oGridSuspensao.show($('ctnGridSuspensao'));

  var oParam = new Object();
  oParam.sExec = "getDadosSupensao";  
  
  var oAjax = new Ajax.Request("orc4_suspensaoorcamentaria.RPC.php", 
                            { method         : 'post',
                              parameters     : 'json=' + js_objectToJson(oParam),//Object.toJSON(oParam),
                              onComplete     : js_preencheGridSuspensao
                            });
}

function js_preencheGridSuspensao(oAjax){

  js_removeObj("msgBox");

  var oRetorno = eval("("+oAjax.responseText+")");

  if (oRetorno.iStatus == "2") {
    alert(oRetorno.sMessage.urlDecode());
  }  
  else {

    oGridSuspensao.clearAll(true);

    for (var i = 0; i < oRetorno.aSuspensao.length; i++) {

      var aCelulas = Array();

      with (oRetorno.aSuspensao[i]) {
        aCelulas[0] = orgao;
        aCelulas[1] = unidade+" - "+o41_descr.urlDecode();
        aCelulas[2] = localizadorgastos;
        aCelulas[3] = recurso;
        aCelulas[4] = "<input id='suspEmpenho_"+sequencial+"' name='suspEmpenho' "+((suspenderempenho=='t')?"checked":"")+" type='checkbox' />";
        aCelulas[5] = "<input id='suspLiquidacao_"+sequencial+"' name='suspLiquidacao' "+((suspenderliquidacao=='t')?"checked":"")+" type='checkbox' />";
        aCelulas[6] = "<input id='suspPagamento_"+sequencial+"' name='suspPagamento' "+((suspenderpagamento=='t')?"checked":"")+" type='checkbox' />";
        aCelulas[7] = sequencial;//"<input name='idSuspensao' value='"+sequencial+"' type='hidden' />";
        aCelulas[8] = exercicio;
        aCelulas[9] = unidade;
      }
      oGridSuspensao.addRow(aCelulas);
    }
    oGridSuspensao.renderRows();
  }
}

function js_atualizarSuspensao(){

  js_divCarregando('Realizando alterações na suspensão...', 'msgBox');

  var aAlteracoesSuspensao = Array();

  for (var i = 0; i < oGridSuspensao.getNumRows(); i++) {
    
    var sequencial = oGridSuspensao.aRows[i].aCells[7].getValue();
    var exercicio = oGridSuspensao.aRows[i].aCells[8].getValue();
    var orgao = oGridSuspensao.aRows[i].aCells[0].getValue();
    var unidade = oGridSuspensao.aRows[i].aCells[9].getValue();
    var anexo = oGridSuspensao.aRows[i].aCells[2].getValue();
    var recurso = oGridSuspensao.aRows[i].aCells[3].getValue();
    var suspensaoEmpenho = document.getElementById('suspEmpenho_'+sequencial).checked;
    var suspensaoLiquidacao = document.getElementById('suspLiquidacao_'+sequencial).checked;
    var suspensaoPagamento = document.getElementById('suspPagamento_'+sequencial).checked;


    aAlteracoesSuspensao[sequencial] = {};
    aAlteracoesSuspensao[sequencial]["exercicio"] = exercicio;
    aAlteracoesSuspensao[sequencial]["orgao"] = orgao;
    aAlteracoesSuspensao[sequencial]["unidade"] = unidade;
    aAlteracoesSuspensao[sequencial]["anexo"] = anexo;
    aAlteracoesSuspensao[sequencial]["recurso"] = recurso;
    aAlteracoesSuspensao[sequencial]["suspensaoEmpenho"] = suspensaoEmpenho ? 'true' : 'false';
    aAlteracoesSuspensao[sequencial]["suspensaoLiquidacao"] = suspensaoLiquidacao ? 'true' : 'false';
    aAlteracoesSuspensao[sequencial]["suspensaoPagamento"] = suspensaoPagamento ? 'true' : 'false'; 


  }

  var oParam = new Object();
  oParam.sExec = "atualizarSuspensao";  
  oParam.aAlteracoes = aAlteracoesSuspensao;

  var oAjax = new Ajax.Request("orc4_suspensaoorcamentaria.RPC.php", 
                            { method         : 'post',
                              parameters     : 'json=' + js_objectToJson(oParam),
                              onComplete     : js_concluirAtualizacao
                            });
}

function js_concluirAtualizacao(oAjax){

  js_removeObj("msgBox");

  var oRetorno = eval("("+oAjax.responseText+")");
  alert(oRetorno.sMessage.urlDecode());
  
  js_criaGridSuspensao();

}

</script>
