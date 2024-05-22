@section('content')
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
        }

        .chat {
            border: 1px solid gray;
            border-radius: 3px;
            width: 50%;
            padding: 0.5em;
        }

        .chat-left {
            background-color: white;
            align-self: flex-start;
        }

        .chat-right {
            background-color: #3f9ae5;
            align-self: flex-end;
        }

        .message-input-container {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: white;
            border: 1px solid gray;
            padding: 1em;


        }
    </style>
    <div class="container" style="margin-bottom: 480px" >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        <div class="chat-container">
                    
                           <p class="chat chat-right">
                               <b>A :</b><br>
                                        message1                              </p>
                                    <p class="chat chat-left">
                                        <b>B :</b><br>
                                        message 2
                                    </p>
                     


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="message-input-container">
        <form action="" method="POST">
            @csrf
            <div class="form-group">
                <label>Message</label>
                <input type="text" name="message" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">SEND MESSAGE</button>
            </div>
        </form>
    </div>
@endsection