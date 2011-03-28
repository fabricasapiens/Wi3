<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Sprig Auth driver.
 *
 * @package    Sprig Auth
 * @author     Paul Banks
 */
class Auth_Sprig extends Auth {

	// Cache the Authenticated User Object
	protected $_user_auth = NULL;

	// Cache the Sprig User Objects
	protected $_user_cache = array();

	// Model Name for Users
	protected $_user_model = NULL;

	// Model Name for Tokens
	protected $_token_model = NULL;
	
	/**
	 * Sets and Gets the Model used for Users
	 *
	 * @param string $model
	 * @return mixed 
	 */
	public function user_model($model = NULL)
	{
		if (isset($model))
		{
			$this->_user_model = $model;
			return $this;
		}

		if ( ! isset($this->_user_model))
		{
			$this->_user_model = (isset($this->_config->user_model))
				? $this->_config->user_model
				: 'User';
		}
		return $this->_user_model;
	}
	
	/**
	 * Sets and Gets the Model used for Tokens
	 *
	 * @param string $model
	 * @return mixed 
	 */
	public function token_model($model = NULL)
	{
		if (isset($model))
		{
			$this->_token_model = $model;
			return $this;
		}
		if ( ! isset($this->_token_model))
		{
			$this->_token_model = (isset($this->_config->token_model))
				? $this->_config->token_model
				: 'User_Token';
		}
		return $this->_token_model;
	}
	
	/**
	 * Checks if a session is active.
	 *
	 * @param   string   role name
	 * @param   array    collection of role names
	 * @return  boolean
	 */
	public function logged_in($role = NULL)
	{
		$status = FALSE;

		// Get the user
		$user = $this->_load_user();
	
		if ($user instanceof Sprig AND $user->loaded())
		{
			// Check for a role requirement
			if ( ! empty($role))
			{
				// See if role is a set
				$status = (is_array($role))
					? $user->has_roles($role)
					: $user->has_role($role);
			}
			else
			{
				// No Role Check, return true
				$status = TRUE;
			}
		}

		return $status;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function _login($user, $password, $remember)
	{
		// Make sure we're logged out first
		if (isset($this->_user_auth))
		{
			$this->logout();
		}

		// Make sure we have a user object
		$login = $this->_get_object($user);

		// If the passwords match, perform a login
		if ($login instanceof Sprig
			AND $login->loaded()
			AND $login->has_role('login')
			AND $login->password === $password)
		{
			if ($remember === TRUE)
			{
				// Create a new autologin token
				$token = $this->new_token($login);

				// Set the autologin cookie
				cookie::set('authautologin', $token->token, $token->expires);
			}

			// Finish the login
			$this->complete_login($login);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	public function force_login($user)
	{
		// Make sure we have a user object
		$login = $this->_get_object($user);
		
		if ($login instanceof Sprig AND $login->loaded())
		{
			// Mark the session as forced, to prevent users from changing account information
			$this->_session->set('auth_forced', TRUE);

			// Run the standard completion
			$this->complete_login($login);
		}
	}

	/**
	 * Generates a new token for the specified user
	 * 
	 * @param  Sprig User
	 * @return Sprig User_Token
	 */
	public function new_token(Sprig $user, $expires = NULL)
	{
		if ($user->loaded())
		{
			// Create a new token
			$token = Sprig::factory($this->token_model());
			$expires = (isset($expires))
				? $expires
				: time() + $this->_config['lifetime'];

			// Set token data
			$token->user = $user->id;
			$token->expires = $expires;
			$token->create();

			return $token;
		}
	}
	
	/**
	 * Logs a user in, based on a one-use token value.
	 * Useful for password resets or email verification
	 *
	 * @param   string  Token value
	 * @return  boolean
	 */
	public function token_login($token)
	{
		// Load the token and user
		if ( ! $token instanceof Sprig)
		{
			$token = Sprig::factory($this->token_model(), array('token' => $token))->load();
		}

		if ($token->loaded()
			AND $token->user->load()
			AND $token->user->loaded()
			AND $token->user->has_role('login'))
		{
			if ($token->user_agent === sha1(Request::$user_agent))
			{
				// Complete the login with the found data
				$this->complete_login($token->user);

				// Delete the token once used
				$token->delete();

				// Token login was successful
				return TRUE;
			}

			// Token is invalid
			$token->delete();
		}
		return FALSE;
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		if ($token = cookie::get('authautologin'))
		{
			// Load the token and user
			$token = Sprig::factory($this->token_model(), array('token' => $token))->load(); 

			if ($token->loaded()
				AND $token->user->load()
				AND $token->user->loaded()
				AND $token->user->has_role('login'))
			{
				if ($token->user_agent === sha1(Request::$user_agent))
				{
					// Save the token to create a new unique token
					$token->update();

					// Set the new token
					cookie::set('authautologin', $token->token, $token->expires - time());

					// Complete the login with the found data
					$this->complete_login($token->user);

					// Automatic login was successful
					return TRUE;
				}

				// Token is invalid
				$token->delete();
			}
		}

		return FALSE;
	}

	/**
	 * Log a user out and remove any auto-login cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @param	boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		if ($token = cookie::get('authautologin'))
		{
			// Delete the autologin cookie to prevent re-login
			cookie::delete('authautologin');
			
			// Clear the autologin token from the database
			$token = Sprig::factory($this->token_model(), array('token' => $token))->delete();
		}

		// Delete all the User's Tokens
		if (isset($this->_user_auth) AND $logout_all)
		{
			Sprig::factory($this->token_model(), array('user' => $this->_user_auth->id))->delete();
		}

		// Removed the cached user
		$this->_user_auth = NULL;
		return parent::logout($destroy);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($user)
	{
		// Make sure we have a user object
		$user = $this->_get_object($user);
		if ($user instanceof Sprig AND $user->loaded())
		{
			return $user->password;
		}
	}
    
    // TODO: fix this function into a real thing
    public function check_password($password)
    {
    }

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles
	 *
	 * @param   object   user model object
	 * @return  void
	 */
	protected function complete_login($user)
	{
		// Update the number of logins
		$user->logins += 1;

		// Set the last login date
		$user->last_login = time();

		// Save the user
		$user->update();

		// Cache the user
		$this->_user_auth = $user;

		// Regenerate session_id
		$this->_session->regenerate();

		// Store User ID in an array...
		$store = array('id' => $user->id);

		// Store user info in session
		$this->_session->set($this->_config['session_key'], $store);
	}

	/**
	 * Gets the currently logged in user from the session.
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		return $this->_load_user();
	}

	/**
	 * Load the user from cache, session, or autologin token
	 * 
	 * @return Model_User
	 */
	protected function _load_user()
	{
		// Return the cached user if set
		if (isset($this->_user_auth)) return $this->_user_auth;

		// Grab the User from the Session
		$user = $this->_session->get($this->_config['session_key'], FALSE);

		// No user in session, try tokens
		if ($user === FALSE)
		{
			// Attempt auto login
			if ($this->auto_login())
			{
				// Success, retrieve the user
				return (isset($this->_user_auth))
					? $this->_user_auth
					: FALSE;
			}
			// Nothing found...
			return FALSE;
		}

		// Load the User Model from the stored array
		$user = Sprig::factory($this->user_model(), $user)->load();

		if($user->loaded() AND $user->has_role('login'))
		{
			// Cache and return User Object
			return $this->_user_auth = $user;
		}

		return FALSE;
	}

	/**
	 * Convert a unique identifier string or a values array to a user object
	 *
	 * @param mixed $user
	 * @return Model_User
	 */
	protected function _get_object($user)
	{
		// Return Existing objects
		if ($user instanceof Sprig) return $user;

		if( ! isset($this->_user_cache[$user]))
		{
			$this->_user_cache[$user] = Sprig::factory($this->user_model())->unique_key($user);
		}

		return $this->_user_cache[$user];
	}

} // End Auth_Sprig_Driver