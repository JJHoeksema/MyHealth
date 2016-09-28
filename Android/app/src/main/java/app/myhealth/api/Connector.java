package app.myhealth.api;

import android.os.AsyncTask;
import android.util.Log;

import java.io.BufferedInputStream;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Created by Werk on 27-9-2016.
 */
public class Connector extends AsyncTask<String, String, String>
{
    private static final String API_URL = "http://myhealth.niekgigengack.nl/api/index/";

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
        String urlString = API_URL + params[0]; // URL to call
        String jsonString = params[1];

        InputStream in;
        try {

            URL url = new URL( urlString );

            HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
            urlConnection.setDoOutput(Boolean.TRUE);
            OutputStreamWriter wr= new OutputStreamWriter(urlConnection.getOutputStream());
            wr.write( jsonString );
            in = new BufferedInputStream(urlConnection.getInputStream());


        } catch (Exception e) {

            System.out.println(e.getMessage());

            return e.getMessage();

        }

        return convertStreamToString(in);
    }

    @Override
    protected void onPostExecute(String result)
    {
        Log.d("Action result", result);
    }

    static String convertStreamToString(java.io.InputStream is) {
        java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
        return s.hasNext() ? s.next() : "";
    }

}
