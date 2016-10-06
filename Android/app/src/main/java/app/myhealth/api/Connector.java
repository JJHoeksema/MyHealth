package app.myhealth.api;

import android.os.AsyncTask;
import android.util.Log;

/**
 * Created by Werk on 27-9-2016.
 */
public class Connector extends AsyncTask<String, String, String>
{

    public Connector(){
        //set context variables if required
    }

    @Override
    protected void onPreExecute()
    {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params)
    {
        return new ApiConnection().connectWithResponse(params);
    }

    @Override
    protected void onPostExecute(String result)
    {
        Log.d("Action result", result);
    }

}
