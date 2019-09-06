<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form method="POST" action="/admin/parkings">
            @csrf
            <input name="names" type="text" placeholder="name" />
            <input name="address" type="text" placeholder="address" />

            <input name="user_id" type="number" placeholder="user_id" />
            <button type="submit">Submit</button>

        </form>
    </body>
</html>




