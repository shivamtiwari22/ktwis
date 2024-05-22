<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Terms & Conditions</title>

    <style>
        .wrap {
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-sizing: border-box;
            height: 100vh;
            padding: 2rem;
            background-color: #eee;
        }

        .container {
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            padding: 1rem;
            background-color: #fff;
            width: 768px;
            height: 100%;
            border-radius: 0.25rem;
            box-shadow: 0rem 1rem 2rem -0.25rem rgba(0, 0, 0, 0.25);

            &__heading {
                padding: 1rem 0;
                border-bottom: 1px solid #ccc;
                text-align: center;

                &>h2 {
                    font-size: 1.75rem;
                    line-height: 1.75rem;
                    margin: 0;
                }
            }

            &__content {
                flex-grow: 1;
                overflow-y: scroll;
            }

            &__nav {
                border-top: 1px solid #ccc;
                text-align: right;
                padding: 2rem 0 1rem;

                &>.button {
                    background-color: #444499;
                    box-shadow: 0rem 0.5rem 1rem -0.125rem rgba(0, 0, 0, 0.25);
                    padding: 0.8rem 2rem;
                    border-radius: 0.5rem;
                    color: #fff;
                    text-decoration: none;
                    font-size: 0.9rem;
                    transition: transform 0.25s, box-shadow 0.25s;

                    &:hover {
                        box-shadow: 0rem 0rem 1rem -0.125rem rgba(0, 0, 0, 0.25);
                        transform: translateY(-0.5rem);
                    }
                }

                &>small {
                    color: #777;
                    margin-right: 1rem;
                }
            }
        }
    </style>
</head>

<body>
    <main class="wrap">
        <section class="container">
            <div class="container__heading">
                <h2>{{ $term ? $term->title : 'Lorem Ipsum' }}</h2>
            </div>
            <div class="container__content">
                @if ($term)
                {!! $term->content !!}
                @else
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of
                type and scrambled it to make a type specimen book. It has survived not only five centuries, but
                also the leap into electronic typesetting, remaining essentially unchanged. It was popularised
                in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more
                recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum
                @endif
            </div>
            <div class="container__nav">
                <small>By clicking 'Accept' you are agreeing to our terms and conditions.</small>
                <br>
                <a class="button" href="{{route('vendor.register')}}">Accept</a>
            </div>
        </section>
    </main>
</body>

</html>
