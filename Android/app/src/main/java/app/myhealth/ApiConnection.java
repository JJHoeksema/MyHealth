package app.myhealth;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Created by Werk on 29-9-2016.
 */
public class ApiConnection
{
    private static final String API_URL = "http://myhealth.niekgigengack.nl/api/index/";

    public String connectWithResponse(String... params)
    {
        String urlString = API_URL + params[0]; // URL to call
        String jsonString = params[1];

        BufferedReader in;
        try {

            URL url = new URL( urlString );

            HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
            urlConnection.setDoOutput(Boolean.TRUE);

            urlConnection.setDoOutput(true);// Should be part of code only for .Net web-services else no need for PHP
            DataOutputStream wr = new DataOutputStream(urlConnection.getOutputStream());
            wr.writeBytes(jsonString);
            wr.flush();
            wr.close();

            in = new BufferedReader(
                    new InputStreamReader(urlConnection.getInputStream()));
            String inputLine;
            StringBuffer response = new StringBuffer();

            while ((inputLine = in.readLine()) != null) {
                response.append(inputLine);
            }
            in.close();

            return response.toString();
        } catch (Exception e) {

            System.out.println(e.getMessage());

            return e.getMessage();

        }

//        return convertStreamToString(in);
    }

    static String convertStreamToString(java.io.InputStream is) {
        java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
        return s.hasNext() ? s.next() : "";
    }

}
