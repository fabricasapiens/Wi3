An extension of The [Kohana 3 Auth module](http://github.com/kohana/auth) for use with [Sprig](http://github.com/shadowhand/sprig) User models.

This module must be added to the active modules array in your bootstrap BEFORE auth module as it overrides some classes.

### Incompatibilities with Auth ORM driver

1.    There is one intentional difference from the ORM driver for Auth module, and that is that logged_in() (and by extension get_user()) will 
automatically call auto_login() if no user session is found. This functionality seems the most sensible to me. 
If this causes problems for you please raise it as an issue or just override Auth_Sprig and change it back.


2.    Additional Token functionality had been added to easily generate additional tokens and then to authenticate with those tokens.
Usefull for email verification and password reset routines. 

### Known Issues

1.    There is an outstanding issue with serializing Database reslts and therefore sprig objects with many realtions.
	See [Sprig issue 40](http://github.com/shadowhand/sprig/issues/#issue/40)
	Since this has gone along time without update, I have included the fix (hack?) in this model to make it work with current Sprig veersion.
	
	UPDATE: Now Auth driver only stores the id of the user rather than the whole serialized user object