<style>

    body {
        max-width: 100% !important;
    }

    table {
        width: 100% !important;
    }

</style>


{strip}
    <section id="{$localId}" class="system_mvc_FormController systems_syssite_padrao_Style">

        {if isset($readerClick) || isset($readerData) || isset($readerUrl)}
            <div class="p-10">
                {*                <div class="mb-10 no-print">*}
                {*                    <a href="">voltar</a>*}
                {*                </div>*}
                <h1>Relatório de Acessos</h1>
                <h3>
                    {$anoFiltro}
                    {if $dataInicio && $dataFim} - {$dataInicio}{/if}
                    {if $dataInicio && $dataFim} - {$dataFim}{/if}
                    {if $dataInicio && $dataFim} - {$dataInicio} até {$dataFim}{/if}
                </h3>
            </div>
            <div class="syssite_Grid ">

                {if isset($readerData)}
                    <div class="Cell C5-1 S8 p-10">
                        <h3>Acessos</h3>
                        <h5>Por dia</h5>
                        {include file="ClicksReport_tableData.tpl"}
                    </div>
                {/if}
                {if isset($readerClick)}
                    <div class="Cell C5-1 S8 p-10">
                        <h3>Acessos</h3>
                        <h5>Dias mais acessados</h5>
                        {include file="ClicksReport_table1.tpl"}
                    </div>
                {/if}
                {*                {if isset($readerSegmentos)}*}
                {*                    <div class="Cell C8-2 S8 p-10">*}
                {*                        <h3>Segmentos/Clicks</h3>*}
                {*                        {include file="ClicksReport_segmentos.tpl"}*}
                {*                    </div>*}
                {*                {/if}*}
                {if isset($readerUrl)}
                    <div class="Cell C5-3 S8 p-10">
                        <h3>Convênios</h3>
                        <h5>Acessos autenticados (logados) aos convênios</h5>
                        {include file="ClicksReport_table2.tpl"}
                    </div>
                {/if}

            </div>
        {else}
            <h1>Clicks</h1>
            <form enctype="multipart/form-data" accept-charset="utf-8" method="post" id="" action="" target="_blank">
                {$defaultField}
            </form>
        {/if}

    </section>
{/strip}