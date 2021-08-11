{strip}
    <section id="{$localId}">

        <form enctype="multipart/form-data" accept-charset="utf-8" method="post" id="" action="">

            <div style="max-width: 500px; margin: auto" class="mt-40">

                <div class="syssite_Grid p-20 bc-white br-8" style="border: 1px solid royalblue">

                    <h2>Buscar hotéis</h2>

                    <div>
                        {$messages}
                    </div>

                    <div class="Cell C1-1 S8 p-10">
                        {$divDestino}
                    </div>

                    <div>
                        <div class="Cell C2-1 S8 p-10">
                            {$dataIda}
                        </div>
                        <div class="Cell C2-1 S8 p-10">
                            {$dataVolta}
                        </div>
                    </div>


                    <div>
                        <div class="Cell C3-1 S8 p-10">
                            {$nrQuartos}
                        </div>
                        <div class="Cell C3-1 S8 p-10">
                            {$nrAdultos}
                        </div>
                        <div class="Cell C3-1 S8 p-10">
                            {$nrCriancas}
                        </div>
                    </div>

                    <div class="Cell C1-1 S8 p-10">
                        {$buttonPesquisar}
                    </div>


                </div>

            </div>

        </form>


    </section>
{/strip}