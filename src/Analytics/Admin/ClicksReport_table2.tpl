{strip}
    <table class="General">

{*        <tr>*}
{*            <td>Clicks</td>*}
{*            <td>URL</td>*}
{*        </tr>*}

        {$total = 0}
        {while $readerUrl->next()}

            {assign var="row" value=$readerUrl->row()}
            {assign var="metaTitle" value=$row['.metaTitle']}
            {assign var="countClicks" value=$row['.countClicks']}
            {assign var="clicks" value=$row['.clicks']}
            {assign var="url" value=$row['.url']}
            {assign var="tagConvenio" value=$row['.tagConvenio']}
            {$total = $total + $clicks}
            <tr>
                <td>{$countClicks + $clicks}</td>
                <td>{if $metaTitle}{$metaTitle|upper}{else}{$tagConvenio|upper}{/if}</td>
            </tr>
        {/while}

        <tr class="fw-bold">
            <td>{$total}</td>
            {*            <td></td>*}
            <td></td>
        </tr>


    </table>
{/strip}