<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excalibur CRUD Demo</title>
    <link rel="stylesheet" href="CSS/Main.css">
</head>
<body>
    <div class="Column AlignCenter">
        <div class="w60 Row">
            <div class="w100 Column w100 Shadow Pad">
                <h2>Add a Guestbook Entry</h2>
                <form id="GuestbookAdd">
                <div class="w100 Column">
                    <div class="w100 Row">
                        <div class="w50 Pad">
                            <label for="Name">Name</label>
                            <input type="text" name="Name" id="Name" class="w100">
                        </div>
                        <div class="w50 Pad">
                            <label for="Email">Email</label>
                            <input type="email" name="Email" id="Email" class="w100">
                        </div>
                    </div>
                    <div class="w100 Row">
                        <div class="w100 Pad">
                            <label for="Message">Message</label>
                            <textarea name="Message" id="Message" class="w100"></textarea>
                        </div>
                    </div>
                    <div class="w100 Row">
                        <div class="w100 Pad">
                            <button type="submit" class="w100 Pad Round BGAccent Row">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</body>
</html>

<!-- CONTROLLERS -->
<script src="ClientSide/Controller/MyFormController.js"></script>

<!-- MAIN FRAMEWORK --> 
<script src="Framework.js"></script>