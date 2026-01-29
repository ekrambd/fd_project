<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Order Status Change</title>
  </head>
  <body>

    <h1>Admin Order Status Change</h1>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.socket.io/4.7.1/socket.io.min.js"></script>

    <script>
      // Node server URL
      const socket = io("https://socket.theclaysbd.xyz");

      const USER_ID = 2; // Example

      socket.emit("join_user_room", USER_ID);


      socket.on("order_status_updated", function(data){
          console.log("New order update:", data);

          //$("#order_" + data.order_id + " .order-status-change").val(data.status);
      });
    </script>

  </body>
</html>
