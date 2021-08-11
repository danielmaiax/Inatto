{strip}
    <table class="General">

        <tr>
            <td>Clicks</td>
            {*            <td>Distinct</td>*}
            <td>URL</td>
        </tr>

        {$total = 0}
        {while $readerSegmentos->next()}

            {assign var="row" value=$readerSegmentos->row()}
            {assign var="countClicks" value=$row['.countClicks']}
            {assign var="clicks" value=$row['click.clicks']}
            {assign var="url" value=$row['.Url']}
            {$total = $total + $clicks}
            <tr>
                <td>{$countClicks + $clicks}</td>
                <td>{$url}</td>
            </tr>
        {/while}

        <tr class="fw-bold">
            <td>{$total}</td>
            {*            <td></td>*}
            <td></td>
        </tr>


    </table>
{/strip}