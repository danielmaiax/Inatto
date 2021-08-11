{strip}
    <table class="General">

        <tr>
            <td>Clicks</td>
            <td>Data</td>
            {*            <td>Distinct</td>*}
        </tr>

        {$total = 0}
        {while $readerClick->next()}

            {assign var="row" value=$readerClick->row()}
            {assign var="clicks" value=$row['.Clicks']}
            {*            {assign var="distinct" value=$row['.Distinct']}*}
            {assign var="data" value=$row['.Data']}
            {$total = $total + $clicks}
            <tr>
                <td>{$clicks}</td>
                <td>{$data}</td>
                {*                <td>{$distinct}</td>*}
            </tr>
        {/while}

        <tr class="fw-bold">
            <td>{$total}</td>
            {*            <td></td>*}
            <td></td>
        </tr>

    </table>
    <div style="position: absolute; width: 100%; top:0; left: 0; text-align: center; padding: 10px;  font-size: 22px; color: black; ">
        {$total|number_format:0:",":"."} acessos
    </div>
{/strip}