package app.myhealth;

import app.myhealth.domain.User;

/**
 * Created by Werk on 4-10-2016.
 */
public abstract class Database
{
    private User _user;


    public User get_user()
    {
        return _user;
    }

    public void set_user(User _user)
    {
        this._user = _user;
    }

}
