<!-- $Id: add.tpl,v 1.1 2011/08/25 08:44:52 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $MODULE_ACTION=='edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
	<label for="name">Name</label><br/>
	<input  autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|_eh}" /><br/>

	<label for="container">Container</label><br/>
	<input  autocomplete="off" type="text" id="container" name="container" size="32"  value="{$FORM.container|_eh}" /><br/>

	<label for="module">Module</label><br/>
	<input  autocomplete="off" type="text" id="module" name="module" size="32"  value="{$FORM.module|_eh}" /><br/>

	<label for="action">Action</label><br/>
	<input  autocomplete="off" type="text" id="action" name="action" size="32"  value="{$FORM.action|_eh}" /><br/>

	<label for="query">Query</label><br/>
	<pre id="query">
{foreach key=key item=val from=$FORM.query}
	{$key} =&gt; {$val}
{/foreach}
	</pre>

	<label for="tags">Tags</label><br/>
	<textarea id="tags" name="tags">{$FORM.tags|_eh}</textarea><br/>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	