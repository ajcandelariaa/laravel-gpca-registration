<html>
    <head>
    <script src="https://ap-gateway.mastercard.com/static/threeDS/1.3.0/three-ds.min.js"
            data-error="errorCallback"
            data-cancel="cancelCallback">
    </script>
    </head>
    <body>
        <div id="3DSUI"></div>
        <input type="hidden" id="session-id" value="{{ session('sessionId') }}">
    </body>

    <script type="text/javascript">
        //The output of this call will return 'false', since the API is not configured yet
        console.log(ThreeDS.isConfigured());
        /**
        Configure method with the configuration{} parameter set and demonstrates the state change of the ThreeDS object before and after the configure method is invoked.
        */
        let sessionId = document.getElementById('session-id').value;
        ThreeDS.configure({
            merchantId: 'TEST900755',
            sessionId: sessionId,
            containerId: "3DSUI",
            callback: function () {
                if (ThreeDS.isConfigured())
                    console.log("Done with configure");
            },
            configuration: {
                userLanguage: "en-AU", //Optional parameter
                wsVersion: 70
            }
        });

        //The output of this call will return 'true', since the API is configured
        console.log(ThreeDS.isConfigured());

        //The output of the following code might look like "ThreeDS JS API Version : 1.2.0"
        console.log("ThreeDS JS API Version : " + ThreeDS.version);
    </script>
</html>