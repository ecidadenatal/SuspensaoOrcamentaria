<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2012  DBselller Servicos de Informatica             
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
require_once(modification("libs/JSON.php")); 
require_once(modification("classes/db_suspensaoorcamentaria_classe.php")); 

$oJson                  = new services_json();
$oParam                 = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oSuspensao             = db_utils::getDao("suspensaoorcamentaria");
$oRetorno               = new stdClass();
$oRetorno->iStatus      = 1;
$oRetorno->sMessage     = '';

$aDadosRetorno          = array();

$aSuspensao             = array();
/**
 * Camada de Tentativas do RPC
 */
try {
  
  switch ($oParam->sExec) {
    
    case "getDadosSupensao":
    
      $oDadosSuspensao = $oSuspensao->buscaSuspensaoDotacao("",db_getsession("DB_anousu"));     
      
      if ( !$oDadosSuspensao ) {
        throw new Exception('Erro ao retornar dados de suspensão.');
      }
      
      $oRetorno->aSuspensao      = array();
      
      if ( pg_num_rows($oDadosSuspensao) ) {
        $oRetorno->aSuspensao    = db_utils::getCollectionByRecord($oDadosSuspensao, false, false,true);
      }
    break;
    
    case "atualizarSuspensao":
      
      db_inicio_transacao();
      //Percorre o array multidimensional que contém os dados das alterações
      foreach ($oParam->aAlteracoes as $iSequencial =>$aDados)
      {
        
        if($iSequencial) {
          
          $oDados = (object) $aDados;
          
          $oSuspensao->suspenderempenho = $oDados->suspensaoEmpenho;
          $oSuspensao->suspenderliquidacao = $oDados->suspensaoLiquidacao;
          $oSuspensao->suspenderpagamento = $oDados->suspensaoPagamento;
          $oSuspensao->exercicio = $oDados->exercicio;
          $oSuspensao->orgao = $oDados->orgao;
          $oSuspensao->unidade = $oDados->unidade;
          $oSuspensao->localizadorgastos = $oDados->anexo;
          $oSuspensao->recurso = $oDados->recurso;
          $oSuspensao->sequencial = $iSequencial; 
          
          $oSuspensao->alterar();

        }
      
        if ($oSuspensao->error_status == "0") {
           throw new Exception("Erro ao atualizar os dados. {$oSuspensao->erro_msg}");   
        }
      
      }

      $oRetorno->sMessage = urlencode("Dados atualizados com sucesso.");
      db_fim_transacao(false);
    
    break;  
       
    default:
      throw new Exception("Nenhuma Operacao Definida");
    break;
  }

} catch (Exception $eErro) {
  
  db_fim_transacao(true);
  $oRetorno->iStatus  = 2;
  $oRetorno->sMessage = urlencode($eErro->getMessage());
}

echo $oJson->encode($oRetorno);