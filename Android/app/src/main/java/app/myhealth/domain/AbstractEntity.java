package app.myhealth.domain;

/**
 * Created by Werk on 27-9-2016.
 */
public abstract class AbstractEntity
{
    private Integer _id;

    public Integer getId()
    {
        return _id;
    }

    public void setId(Integer id)
    {
        this._id = id;
    }

}
