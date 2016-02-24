<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2014 DBSeller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
class cl_suspensaoorcamentaria extends DAOBasica {
	public function __construct() {
		parent::__construct ( "plugins.suspensaoorcamentaria" );
	}
	
	public function buscaSuspensaoDotacao($iDotacao, $iAno) {

		$sWhere = "";
		$sInner = "";		
		if (!empty($iDotacao)) {
			$sWhere = " and orcdotacao.o58_coddot = {$iDotacao} ";
			$sInner = "inner join orcdotacao on o58_unidade           = unidade
	                                        and o58_orgao             = orgao
	                                        and o58_anousu            = exercicio
	                                        and o58_codigo            = recurso
	                                        and o58_localizadorgastos = localizadorgastos";
		}

		$sSqlValidaSuspensaoOrcamentaria = "select distinct *
	                                          from plugins.suspensaoorcamentaria
	                                               {$sInner}
	                                               inner join orcunidade on o41_unidade = unidade
	                                           						    and o41_orgao = orgao
	                                           						    and o41_anousu = exercicio
	                                         where exercicio = {$iAno}
			                                       {$sWhere} 
			                                 order by orgao, unidade, localizadorgastos, recurso";

		$rsSuspensao = $this->sql_record($sSqlValidaSuspensaoOrcamentaria);
		if ($this->numrows > 0) {
		  return $rsSuspensao;	
		} else {
		  return null;
		}
		
	}
}