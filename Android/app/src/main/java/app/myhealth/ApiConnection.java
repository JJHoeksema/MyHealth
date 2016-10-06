package app.myhealth;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;

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
        String data = "";
        try
        {
            data = URLEncoder.encode("content", "UTF-8")
                    + "=" + URLEncoder.encode(jsonString, "UTF-8");
        }
        catch (UnsupportedEncodingException e)
        {
            e.printStackTrace();
        }

        BufferedReader in;
        try
        {
            URL url = new URL( urlString );

            URLConnection conn = url.openConnection();
            conn.setDoOutput(true);
            OutputStreamWriter wr = new OutputStreamWriter(conn.getOutputStream());
            wr.write( data );
            wr.flush();
            wr.close();

            in = new BufferedReader(
                    new InputStreamReader(conn.getInputStream()));
            String inputLine;
            StringBuffer response = new StringBuffer();

            while ((inputLine = in.readLine()) != null) {
                response.append(inputLine);
            }

            in.close();

            return response.toString();
        } catch (Exception e)
        {
            return e.getMessage();

        }
    }

}