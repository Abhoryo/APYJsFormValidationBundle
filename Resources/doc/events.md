Events
======

The Bundle dispatch two events, one before the processing of constraints of a form and the other after this processing.

## Pre processing event

Identifier : `jsfv.pre_process`

### Input arguments :
- FormView : The Form view object of the form
- ClassMetadata : Metadata of the entity/document used by the form

Exemple : [Validation groups] (https://github.com/APY/APYJsFormValidationBundle/blob/master/Listener/ValidationGroupsListener.php)

## Post processing event

Identifier : `jsfv.post_process`

### Input arguments :
- FormView : The Form view object of the form
- FieldsConstraints : Compiled Object used to generate the validation script

Exemple : [Repeated field] (https://github.com/APY/APYJsFormValidationBundle/blob/master/Listener/RepeatedFieldListener.php)
