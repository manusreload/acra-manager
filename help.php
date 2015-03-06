<!DOCTYPE html>
<html lang="es">
    <head>
        <?php require_once "fragment/head.php"; ?>
    </head>

    <body>

        <?php require_once "fragment/menu.php"; ?>

        <div class="container">

            <section id="info">
                <h1>How to Setup Android App</h1>
                
                <h3>Add dependency</h3>
                <p>First, we need to include the library as a dependency in one of the following ways:</p>
                <ul>
                    <li>

                        As a <a href="http://search.maven.org/remotecontent?filepath=ch/acra/acra/4.5.0/acra-4.5.0.jar">.jar</a> file in your /libs folder.

                    </li>
                    <li>
                        As a maven dependency:
                        <pre>
&lt;dependency&gt;
    &lt;groupId&gt;ch.acra&lt;/groupId&gt;
    &lt;artifactId&gt;acra&lt;/artifactId&gt;
    &lt;version&gt;X.Y.Z&lt;/version&gt;
&lt;/dependency&gt;</pre>
                    </li>
                    <li>
                        As a gradle dependency:

                        <code>
                            compile 'ch.acra:acra:4.5.0'
                        </code>
                    </li>
                </ul>
                <h3>Add Application class</h3>

                <p>Next, we need to add an Android Application class to our project (or update an existing class, as there can only be one instance) and declare it in the AndroidManifest.xml:</p>
                <pre>&lt;application
    android:name=&quot;.MyApp&quot;
    android:theme=&quot;@style/AppTheme&quot;&gt;
    ...
                </pre>
                And setup ACRA there:
                <pre>
import android.app.Application;

import org.acra.ACRA;
import org.acra.ReportField;
import org.acra.ReportingInteractionMode;
import org.acra.annotation.ReportsCrashes;
import org.acra.sender.HttpSender;

@ReportsCrashes(
    formUri = &quot;http://<?= $_SERVER['HTTP_HOST'] ?>/crash.php?app=[appId]&quot;,
    httpMethod = HttpSender.Method.POST,
    mode = ReportingInteractionMode.SILENT
)

public class MainApp extends Application {

    @Override
    public void onCreate() {
        super.onCreate();
        // The following line triggers the initialization of ACRA
        ACRA.init(this);
    }
}</pre>
            </section>

            <?php require_once "fragment/footer.php"; ?>

        </div>

        <?php require_once "fragment/scripts.php"; ?>

    </body>
</html>