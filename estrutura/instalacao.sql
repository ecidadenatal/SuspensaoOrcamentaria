create table plugins.suspensaoorcamentaria (
        sequencial integer,
        exercicio integer,
        orgao integer,
        unidade integer,
        localizadorgastos integer,
        recurso integer,
        suspenderempenho boolean,
        suspenderliquidacao boolean,
        suspenderpagamento boolean
);

CREATE SEQUENCE plugins.suspensaoorcamentaria_sequencial_seq;

insert into plugins.suspensaoorcamentaria 
select nextval('plugins.suspensaoorcamentaria_sequencial_seq'), 
       o58_anousu, 
       o58_orgao, 
       o58_unidade, 
       o58_localizadorgastos,o58_codigo,
       'f' as suspenderempenho, 
       'f' as suspenderliquidacao, 
       'f' as suspenderpagamento
  from orcamento.orcdotacao
 where o58_anousu =2015
 group by o58_anousu, o58_orgao, o58_unidade, o58_localizadorgastos,o58_codigo
order by o58_orgao,o58_unidade,o58_localizadorgastos,o58_codigo;
