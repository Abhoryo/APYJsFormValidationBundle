#2012-08-16
- [Tests] Covered uniqueEntity

#2012-08-16
- Implemented phpunit tests. Fixed composer for travis integration.
- Fixed issue #13. Fields with property_path = false must be excluded from validation.
- Fixed issue #16. No need to supply second parameter to createForm controller function.
- [cache warmer] Changed the naming of the output javascript file.
	After:
		{{ JSFV(form, true) }} returns  /bundle/jsformvalidation/js/myRoute_myForm.js
	Before:
		{{ JSFV(form, true) }} has returned  /bundle/jsformvalidation/js/myRoute.js

#2012-08-12
- Implemented client-side validation of simple forms which are built manually.

#2012-08-10
- Added CheckMX support for Email Constraint
- Added getJsFormElementValue(field) Twig Extention
- Changed interface of JS calls. Field object is passed instead of value by default.

#2012-08-06
- Unique Entity support has been implemented (jquery framework only)
- Implemented validation of the constraints which are based on method of the entity clas

#2012-08-04
- Version 2.1 has been mastered.
- Fix issue #8 Symfony 2.1 compability

#2011-12-05
- Dispatch events before and after processing constraints of a form
- Manage validation groups with an event listener
- Manage repeated field
- Include the asset helper function
- Doc for events

#2011-12-01
- New assets warmer system. Now, define routes in configuraiton instead of a list of parameters
- fix isIPv6_no_res function in IpValidator

#2011-11-29
- Search for the first parent form