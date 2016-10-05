package app.myhealth;

import app.myhealth.domain.User;

/**
 * Created by Werk on 4-10-2016.
 */
public abstract class Database
{
    private static User _user;


    public static User getUser()
    {
        return _user;
    }

    public static void setUser(User user)
    {
        _user = user;
    }

}
