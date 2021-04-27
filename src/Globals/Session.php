<?php

namespace Spl\Globals;

class Session
{

    /**
     * starts a session
     */
    public function start()
    {
        // if no session exist, start a new session
        if (session_id() == '') {
            session_start();
        }
    }

    /*
     * Check if session exists or not
     */
    public function exists($key = null)
    {
        return (isset($_SESSION[$key])) ? true : false;
    }

    /*
     * Check if session exists or not
     */
    public function has($key = null)
    {
        return $this->exists($key);
    }

    /*
     * Set a session by key and value
     */
    public function put($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * Adds a value as a new array element to the key.
     * useful for collecting error messages etc
     */
    public function addKey($key, $name, $value)
    {
        $_SESSION[$key][$name] = $value;
    }

    /*
     * Get a session value by key
     */
    public function get($key = null)
    {
        return $_SESSION[$key];
    }

    public function all()
    {
        return $_SESSION;
    }

    /*
     * Get a sessions array value by key and value
     */
    public function getKey($key, $value)
    {
        return $_SESSION[$key][$value];
    }

    /*
     * Delete key value from session array
     */
    public function deleteKey($key, $value)
    {
        unset($_SESSION[$key][$value]);
    }

    /*
     * Arra push to arrays together
     */
    public static function push($key, $value)
    {
        return array_push($_SESSION[$key], $value);
    }

    /*
     * Delete session by key name
     */
    public function delete($key)
    {
        if ($this->has($key))
        {
            unset($_SESSION[$key]);
        }
    }

    /**
     * deletes the session (= logs the user out)
     */
    public function destroy()
    {
        session_destroy();
    }

    /*
     * Flash messages by deleting session after it is shown
     */
    public function flash($key, $string = null)
    {
        if ($this->has($key)) {
            $session = $this->get($key);
            $this->delete($key);
            return $session;
        }

        return false;
    }

}