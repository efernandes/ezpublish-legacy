{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{let class_content=$attribute.class_content
     class_list=fetch( class, list, hash( class_filter, $class_content.class_constraint_list ) )
     can_create=true()
     new_object_initial_node_placement=false()
     browse_object_start_node=false()}

{if is_set( $attribute.class_content.default_placement.node_id )}
    {set browse_object_start_node=$attribute.class_content.default_placement.node_id}
{/if}

{* Optional controls. *}
{include uri='design:content/datatype/edit/ezobjectrelationlist_controls.tpl'}

{* Advanced interface. *}
{section show=eq( ezini( 'BackwardCompatibilitySettings', 'AdvancedObjectRelationList' ), 'enabled' )}

{section show=$attribute.content.relation_list}
<table class="list" cellspacing="0">
<tr class="bglight">
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" onclick="ezjs_toggleCheckboxes( document.editform, '{$attribute_base}_selection[{$attribute.id}][]' ); return false;" title="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" /></th>
    <th>Name</th>
    <th>Type</th>
    <th>Section</th>
    <th class="tight">Order</th>
</tr>

{section name=Relation loop=$attribute.content.relation_list sequence=array( bglight, bgdark )}

<tr class="{$:sequence}">

{section show=$:item.is_modified}

{* Remove. *}
<td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]" value="{$:item.contentobject_id}" /></td>

<td colspan="3">

{let object=fetch( content, object, hash( object_id, $:item.contentobject_id, object_version, $:item.contentobject_version ) )
     version=fetch( content, version, hash( object_id, $:item.contentobject_id, version_id, $:item.contentobject_version ) )}
<fieldset>
<legend>{'Edit <%object_name> [%object_class]'|i18n( 'design/standard/content/datatype',, hash( '%object_name', $Relation:object.name, '%object_class', $Relation:object.class_name ) )|wash}</legend>

{section name=Attribute loop=$:version.contentobject_attributes}
<div class="block">
{section show=$:item.display_info.edit.grouped_input}
<fieldset>
<legend>{$:item.contentclass_attribute.name}</legend>
{attribute_edit_gui attribute_base=concat( $attribute_base, '_ezorl_edit_object_', $Relation:item.contentobject_id ) html_class='half' attribute=$:item}
</fieldset>
{section-else}
<label>{$:item.contentclass_attribute.name}:</label>
{attribute_edit_gui attribute_base=concat( $attribute_base, '_ezorl_edit_object_', $Relation:item.contentobject_id ) html_class='half' attribute=$:item}
{/section}

{* Edit. *}
{section show=$:item.is_modified|not}
    <input type="image" name="CustomActionButton[{$attribute.id}_edit_objects_{$:item.contentobject_id}]" value="{'Edit'|i18n( 'design/standard/content/datatype' )}" src={'edit.gif'|ezimage} />
{section-else}
    &nbsp;
{/section}

</div>
{/section}

{/let}
</fieldset>
</td>

{* Order. *}
<td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]" value="{$:item.priority}" /></td>

{section-else}

{let object=fetch( content, object, hash( object_id, $:item.contentobject_id, object_version, $:item.contentobject_version ) )}

{* Remove. *}
<td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]" value="{$:item.contentobject_id}" /></td>

{* Name *}
<td>{$Relation:object.name|wash()}</td>

{* Type *}
<td>{$Relation:object.class_name|wash()}</td>

{* Section *}
<td>{fetch( section, object, hash( section_id, $Relation:object.section_id ) ).name|wash()}</td>

{* Order. *}
<td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]" value="{$:item.priority}" /></td>

{/let}
{/section}

</tr>

{/section}
</table>
{section-else}
<p>{'There are no related objects.'|i18n( 'design/standard/content/datatype' )}</p>
{/section}

{section show=$attribute.content.relation_list}
<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_remove_objects]" value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}" />&nbsp;
<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_edit_objects]" value="{'Edit selected'|i18n( 'design/standard/content/datatype' )}" />
{section-else}
<input class="button-disabled" type="submit" name="CustomActionButton[{$attribute.id}_remove_objects]" value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}" disabled="disabled" />&nbsp;
<input class="button-disabled" type="submit" name="CustomActionButton[{$attribute.id}_edit_objects]" value="{'Edit selected'|i18n( 'design/standard/content/datatype' )}" disabled="disabled" />
{/section}


{section show=array( 0, 2 )|contains( $class_content.type )}
<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_objects]" value="{'Add objects'|i18n( 'design/standard/content/datatype' )}" />
{section show=$browse_object_start_node}
<input type="hidden" name="{$attribute_base}_browse_for_object_start_node[{$attribute.id}]" value="{$browse_object_start_node|wash}" />
{/section}
{section-else}
<input class="button-disabled" type="submit" name="CustomActionButton[{$attribute.id}_browse_objects]" value="{'Add objects'|i18n( 'design/standard/content/datatype' )}" disabled="disabled" />
{/section}


{section show=and( $can_create, array( 0, 1 )|contains( $class_content.type ) )}
<div class="block">
<select class="combobox" name="{$attribute_base}_new_class[{$attribute.id}]">
{section name=Class loop=$class_list}
<option value="{$:item.id}">{$:item.name|wash}</option>
{/section}
</select>
{section show=$new_object_initial_node_placement}
<input type="hidden" name="{$attribute_base}_object_initial_node_placement[{$attribute.id}]" value="{$new_object_initial_node_placement|wash}" />
{/section}

<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_new_class]" value="{'Create new object'|i18n( 'design/standard/content/datatype' )}" />
</div>
{/section}


{* Simple interface. *}
{section-else}

{section show=$attribute.content.relation_list}

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" onclick="ezjs_toggleCheckboxes( document.editform, '{$attribute_base}_selection[{$attribute.id}][]' ); return false;" title="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" /></th>
    <th>Name</th>
    <th>Type</th>
    <th>Section</th>
    <th class="tight">Order</th>
</tr>
{section var=Objects loop=$attribute.content.relation_list sequence=array( bglight, bgdark )}
{let object=fetch( content, object, hash( object_id, $Objects.item.contentobject_id ) )}

<tr class="{$Objects.sequence}">
{* Remove. *}
<td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]" value="{$Objects.item.contentobject_id}" /></td>

{* Name *}
<td>{$object.name|wash()}</td>

{* Type *}
<td>{$object.class_name|wash()}</td>

{* Section *}
<td>{fetch( section, object, hash( section_id, $object.section_id ) ).name|wash()}</td>

{* Order. *}
<td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]" value="{$Objects.item.priority}" /></td>

</tr>
{/let}
{/section}
</table>

{section-else}

<p>{'There are no related objects.'|i18n( 'design/standard/content/datatype' )}</p>
{/section}
{section show=$attribute.content.relation_list}
<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_remove_objects]" value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}" />&nbsp;
{section-else}
<input class="button-disabled" type="submit" name="CustomActionButton[{$attribute.id}_remove_objects]" value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}" disabled="disabled" />&nbsp;
{/section}
{section show=$browse_object_start_node}
<input type="hidden" name="{$attribute_base}_browse_for_object_start_node[{$attribute.id}]" value="{$browse_object_start_node|wash}" />
{/section}
<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_objects]" value="{'Add objects'|i18n( 'design/standard/content/datatype' )}" />

{/section}

{/let}
