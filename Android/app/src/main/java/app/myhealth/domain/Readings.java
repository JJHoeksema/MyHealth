package app.myhealth.domain;

import com.google.gson.annotations.SerializedName;

import java.sql.Date;

/**
 * Created by Tom on 15-10-2016.
 */
public class Readings extends AbstractEntity
{

    @Override
    public Integer getId()
    {
        return _id;
    }

    @Override
    public void setId(Integer id)
    {
        this._id = id;
    }

    @SerializedName("Readings-id")
    private Integer _id;

    @SerializedName("Readings-naam")
    private String _naam;

    @SerializedName("Readings-type")
    private Integer _type;

    @SerializedName("Readings-user_id")
    private Integer _user_id;

    @SerializedName("Readings-value")
    private String _value;

    @SerializedName("Readings-date")
    private Date _datum;


    public void setNaam(String naam)
    {
        this._naam = naam;
    }

    public String getNaam(){ return _naam; }


    public void setType(int type)
    {
        this._type = type;
    }

    public Integer getType(){ return _type; }


    public void setUser_id(int user_id)
    {
        this._user_id = user_id;
    }

    public Integer getUser_id(){ return _user_id; }


    public void setValue(String value)
    {
        this._value = value;
    }

    public String getValue(){ return _value; }


    public Date getDatum()
    {
        return _datum;
    }

    public void setDatum(Date _datum)
    {
        this._datum = _datum;
    }
}
