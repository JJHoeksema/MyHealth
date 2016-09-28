package app.myhealth.api;

import java.util.concurrent.ExecutionException;

/**
 * Created by Werk on 27-9-2016.
 */
public class ApiCall
{

    public static String getJsonUser(int id)
    {
        return executeGet("users.php&id="+id);
    }

    public static String getJsonUser(String jsonAuthenticate)
    {
        return executePostWithResult("login", jsonAuthenticate);
    }

    public static String getJsonMeasurement(int id)
    {
        return executeGet("measurements.php&id="+id);
    }

    public static String getJsonMeasurements()
    {
        return executeGet("measurements.php");
    }

    private static String executePostWithResult(String call, String postData)
    {
        return executeGet(call, postData);
    }

    private static void executePostWithoutResult(String call, String postData)
    {
        new Connector().execute(call, postData);
    }

    private static String executeGet(String... call)
    {
        Connector connector = new Connector();

        String result = null;
        try
        {
            result = connector.execute(call).get();
        }
        catch (InterruptedException e)
        {
            e.printStackTrace();
        }
        catch (ExecutionException e)
        {
            e.printStackTrace();
        }

        return result;
    }


}
