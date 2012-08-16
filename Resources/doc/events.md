Events
======

The Bundle dispatches two events, one before the processing of constraints of a form and the other after this processing.

## Pre processing event

Identifier : `jsfv.pre_process`

### Input arguments :
- FormView : The Form view object of the form
- ClassMetadata : Metadata of the entity/document used by the form

Example : [Validation groups] (https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/EventListener/ValidationGroupsListener.php)

## Post processing event

Identifier : `jsfv.post_process`

### Input arguments :
- FormView : The Form view object of the form
- FieldsConstraints : Compiled Object used to generate the validation script

Example : [Repeated field] (https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/EventListener/RepeatedFieldListener.php)
