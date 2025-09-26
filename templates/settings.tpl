<script>
    $(function() {ldelim}
        // Attach the form handler.
        $('#getftrSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    {rdelim});
</script>

<form class="pkp_form" id="getftrSettingsForm" method="post" action="{url router=PKP\core\PKPApplication::ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	
	{csrf}

	<p>
		{translate key="plugins.generic.getftr.settings.info"}
	</p>

	<br />
	<hr />
	<br />

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="getftrSettingsFormNotification"}
	
	{fbvFormArea id="getftrSettingsFormArea"}

		{fbvFormSection list=true}
			{fbvElement type="text" id="integratorId" placeholder="plugins.generic.getftr.settings.integratorId" value=$integratorId label="plugins.generic.getftr.settings.integratorId"}
		{/fbvFormSection}

	{/fbvFormArea}

	{fbvFormButtons}
</form>
