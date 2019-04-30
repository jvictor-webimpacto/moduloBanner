<head>
	<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
</head>
<body>
    {if $image}
    <div class="clearfix"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix" >
            <div class="row row-flex">
                <div >
                    <img src="{$path}" class="img-responsive center-block" />
                </div>
            </div>
        </div>
    {/if}
</body>
