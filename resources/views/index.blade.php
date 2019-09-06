<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <fieldset>
                <legend>Create Parkings</legend>
                <form method="POST" action="/admin/parkings">
                    @csrf
                    <input name="names" type="text" placeholder="name" /><br />
                    <input name="address" type="text" placeholder="address" /><br />
        
                    <input name="user_id" type="number" placeholder="user_id" /><br />
                    <button type="submit">Submit</button>
        
                </form>
        </fieldset>
        <fieldset>
                <legend>Create parkingAdmins</legend>
                <form method="POST" action="/admin/parkingAdmins">
                    @csrf
                    <input type="text" name="username" placeholder="Username" /><br />
                    <input type="text" name="email" placeholder="Email" /><br />
                    <input type="password" name="password" placeholder="password" /><br />
                    <input type="password" name="c_password" placeholder="comfirm password" /><br />
                    <input type="text" name="level" placeholder="level" /><br />
                    <input type="text" name="name" placeholder="Name" /><br />
                    <button type="submit">Submit</button>
                </form>
        </fieldset>

    </body>
</html>




