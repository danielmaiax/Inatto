{strip}
    <table class="General">

        <tr>
            <td>Data</td>
            <td>Clicks</td>
            {*            <td>Distinct</td>*}
        </tr>

        {$total = 0}
        {while $readerData->next()}

            {assign var="row" value=$readerData->row()}
            {assign var="clicks" value=$row['.Clicks']}
            {*            {assign var="distinct" value=$row['.Distinct']}*}
            {assign var="data" value=$row['.Data']}
            {$total = $total + $clicks}
            <tr>
                <td>{$data}</td>
                <td>{$clicks}</td>
                {*                <td>{$distinct}</td>*}
            </tr>
        {/while}

        <tr class="fw-bold">
            <td></td>
            <td>{$total}</td>
            {*            <td></td>*}
        </tr>

    </table>
{/strip}