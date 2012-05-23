login#!#<div id="loginbox">
	<div class="title">login</div>
	<form name="login_form" id="login_form" action="">
		<label for="u_login">username:</label>
		<input type="text" name="u_login" id="u_login" value="" class="input_text" /><br />
		<label for="u_password">password:</label>
		<input type="password" name="u_password" id="u_password" value="" class="input_text" /><br />
		<input type="submit" id="loginbutton" class="button" value="login"> <div id="loginresult"></div>
	</form>
</div>##!##new_collection#!#<div>Please enter a Name for the new Timesheet Collection:</div>
<input type="text" name="new_c_name" value="" id="new_c_name" style="font-size:14px; width:200px; border:1px solid #CCCCCC; " /><br /> <br />
<div id="newcollection_submit" class="button">create </div> <div class="button" style="float:left;" onclick="BC.broadcastMessage('_close_modal');">cancel </div>
<div id="response" style="clear:both; margin-top:10px;"></div>
##!##new_timesheet#!#<div>Please enter a Name for the Timesheet:</div>
<input type="text" name="new_title" value="" id="newProject_title" style="font-size:14px; width:200px; border:1px solid #CCCCCC; " /><br /> <br />
<div id="newproject_submit" class="button">create </div> <div class="button" style="float:left;" onclick="BC.broadcastMessage('_close_modal');">cancel </div>
<div id="create_project_response" style="clear:both; margin-top:10px;"></div>
##!##summary#!#<div id="summary">
	<select class="collectionlist"></select>
	<div class="new_collection">New Collection</div>
		
	<div class="canvas">
		<div class="tabs">
			<div class="timesheets">Timesheets</div>
			<div class="details">Collection Details</div>
			<div class="delete">Delete Collection</div>
		</div>
		
		<div class="content visible">
			<div class="timesheets">
				<ul id="summary_list">
		
				</ul>
				<div id="bu_newproject">New Timesheet</div>
			</div>
			
			<div class="details hidden" style="display:none">
				<table>
					<tr>
						<td>Name:</td>
						<td><input type="text" value="" id="c_name" /></td>
					</tr>
					<tr>
						<td>Info:</td>
						<td><textarea id="c_info" style="height:100px;"></textarea><br></td>
					</tr>
					<tr>
						<td></td>
						<td><div class="update button">update</div></td>
					</tr>
				 </table>
			</div>
			
			<div class="delete hidden">
				<div style="padding:20px">
					Delete this Timesheet Collection? All Timesheets will be lost!!!<br /><br />
					<input type="checkbox" id="collection_delete_confirm" /> Yes pleeze!<br />
					<div id="bu_delete" class="button" onclick="currentcollection.obliterate();">Delete</div>
				</div>
			</div>
		</div>
	</div>
</div>##!##tasklist_entry#!#{if !asNode}<div id="row_${task_id}" class="t_row ${rowclass}">{/if}

 <input class="c1" type="text" id="${task_id}_t_feature" value="${t_feature}" />
 <textarea class="c2" id="${task_id}_t_description" >${t_description|htmlentities}</textarea>
 <div class="tl_right" >
  <div class="c3" id="${task_id}_t_prio">${task_id|drawPrio:t_prio}</div>
  <input class="c4" type="text" id="${task_id}_t_origest" value="${t_origest|s2h}" />
  <input class="c5" type="text" id="${task_id}_t_currest" value="${t_currest|s2h}" />
  <input class="c6" type="text" id="${task_id}_t_elapsed" value="${t_elapsed|s2h}" />
  <div class="c7" id="${task_id}_t_remain">${t_remain|s2h}</div>
  <div class="c10"><div class="readystat" id="hk_${task_id}" onclick="task_setReady(this);" ></div><div class="swb" id="ti_${task_id}" onclick="stopwatch.click(this);" ></div><div class="sort"></div></div>
 </div>
 
{if !asNode}</div>{/if}##!##tasktable#!#
<div id="tasklist" >
	<div id="tasklist_top">
		<div id="tasklist_head" class="t_row">
			<div class="c0">&nbsp;</div>
			<div class="c1">&nbsp;</div>
			<div class="c2">&nbsp;</div>
			<div class="tl_right">
				<div class="c3">prio</div>
				<div class="c4">orig.</div>
				<div class="c5">curr.</div>
				<div class="c6">done</div>
				<div class="c7">remain</div>
				<div class="c10">&nbsp;</div>
			</div>
		</div>
		<div id="tasklist_summary" class="t_row">
			<div class="c0">&nbsp;</div>
			<div class="c1">Summary:</div>
			<div  class="tl_right">
				<div class="c3" id="summary_prio">&nbsp;</div>
				<div class="c4" id="summary_origest">&nbsp;</div>
				<div class="c5" id="summary_currest">&nbsp;</div>
				<div class="c6" id="summary_elapsed">&nbsp;</div>
				<div class="c7" id="summary_remain">&nbsp;</div>
				<div class="c10">&nbsp;</div>
			</div>
		</div>
	</div>
	
	<div id="tasklist_list">
		<div class="content_loading"><div id="loading_status">loading tasklist...</div></div>
	</div>
</div>

##!##useredit#!#<div style="width:400px;" id="preferences">

<label for="p_name">Name: </label> <input type="text" name="p_name" id="p_name" value="${u_name}" />
<label for="p_name">Email:</label> <input type="text" name="p_email" id="p_email" value="${u_email}" />
<label for="p_name">Username:</label> <input type="text" name="p_login" id="p_login" value="${u_login}" />

<div style="float:left; margin-top:20px; clear:both;"><span style="font-weight:bold; clear:both; margin-bottom:5px;">reset password:</span></div>

<label for="p_name">password:</label><input type="password" name="p_password_new" id="p_password_new" value="" />
<label for="p_name">retype password:</label><input type="password" name="p_password_retype" id="p_password_retype" value="" />
<input type="button" class="button" name="save" value="update" style="clear:both; margin-top:18px; float:left;" onclick="user.save();" />
<div id="response" style="margin-top:20px; float:left; clear:both;"></div>
</div>##!##taskdetails_details#!#<div class="details_files">
	<b>Attached Files:</b><br />
	<div class="details_fileslist" id="details_fileslist_${task_id}">
		
		
		
	</div>
	<div class="details_upload">
			<object width="300" height="30">
				<param name="uploader" value="plugins/taskdetails/upload.swf?taskID=${task_id}&sessionID=[{$sessionID}]"/>
				<param name="allowScriptAccess" value="sameDomain" />
				<embed src="plugins/taskdetails/upload.swf?taskID=${task_id}&sessionID=${sessionID}" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" wmode="transparent" width="300" height="30">
				</embed>
			</object>
		</div>
</div>##!##