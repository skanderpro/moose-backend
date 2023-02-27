@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="/html/dist/files/css/jquery.bracket.min.css?_v=20230212010729" />

    <main
        class="tournament-bracket-bg"
        style="background-image: url(/html/dist/img/ncaa-bg.svg)"
    >
        <div class="tournament-bracket-wrapper">
            <div class="container">
                <h1 class="g-title">{{ $season ? $season->title : '' }}</h1>
                <div id="gesture-area">
                    <div id="scale-element" class="tournament-bracket" >
                        <div id="save"></div>
                        <div id="final">
                            <div class="final-title">Final Four</div>
                        </div>
                        <div id="save1"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="/html/dist/files/js/jquery.min.js?_v=20230212010729"></script>
    <script src="/html/dist/files/js/jquery.bracket.min.js?_v=20230212010729"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js?_v=20230212010729"></script>

    <script type="text/javascript">
        $(function () {
            var demos = ["save", "save1", "final"];
            $.each(demos, function (i, d) {
                var demo = $("div#" + d);
                $('<div class="demo"></div>').appendTo(demo);
            });
        });
    </script>
    <script type="text/javascript">
        const userId = {{ \Illuminate\Support\Facades\Auth::id() }};
        const isCurrentSeason = {{NOW()->isAfter($season->start) ? 'false' : 'true'}};
        const save = (type) => !isCurrentSeason ? undefined : (data) => {
            fetch('{{ route("scores.store", ["season" => $season]) }}', {
                method: 'post',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json; charset=utf-8'
                },
                body: JSON.stringify({
                    type,
                    results: data.results
                })
            })
        };

        var finalData = {
            teams: [],
            results: {!! json_encode($final) !!},
        };
        var saveData1 = {
            teams: {!! json_encode($games_right) !!},

            results: {!! json_encode($right) !!},
        };
        var saveData = {
            teams: {!! json_encode($games_left) !!},

            results: {!! json_encode($left) !!},
        };
        /* Called whenever bracket is modified
         *
         * data:     changed bracket object in format given to init
         * userData: optional data given when bracket is created.
         */
        function saveFn(data, userData) {
            var json = jQuery.toJSON(data);

            $("#saveOutput").text(JSON.stringify(data, null, 2));
            /* You probably want to do something like this
               jQuery.ajax("rest/"+userData, {contentType: 'application/json',
               dataType: 'json',
               type: 'post',
               data: json})
               */
        }

        function triggerNewScore($team, score, tag) {
            return new Promise(resolve => {
                const $score = $team.find('.score');
                $score[0].click();

                setTimeout(() => {
                    const $input = $score.find('input');
                    $input.val(score);
                    $input[0].blur();

                    setTimeout(() => resolve(true), 100);
                }, 100);
            });
        }

        /*for flag*/
        /* Edit function is called when team label is clicked */
        function edit_fn($container, data, doneCb) {
            const $team = $container.closest('.team');
            const $opponent = $team.prev('.team').length ? $team.prev('.team') : $team.next('.team');
            const $teamScore = $team.find('.score').data('resultid');
            const $wrapper = $team.closest('.jQBracket');

            triggerNewScore($opponent, 0, 'opponent').then(() => triggerNewScore($wrapper.find('[data-resultid="' + $teamScore + '"]').closest('.team'), 1, 'team'));

        }

        /* Render function is called for each team label when data is changed, data
         * contains the data object given in init and belonging to this slot.
         *
         * 'state' is one of the following strings:
         * - empty-bye: No data or score and there won't team advancing to this place
         * - empty-tbd: No data or score yet. A team will advance here later
         * - entry-no-score: Data available, but no score given yet
         * - entry-default-win: Data available, score will never be given as opponent is BYE
         * - entry-complete: Data and score available
         */
        function render_fn(container, data, score, state) {
            switch (state) {
                case "empty-bye":
                    container.append("No team");
                    return;
                case "empty-tbd":
                    container.append("Upcoming");
                    return;

                case "entry-no-score":
                case "entry-default-win":
                case "entry-complete":
                    container
                        .append(`<span class="team-name">(${data.rating}) ${data.name}</span>`);
                    return;
            }
        }

        $(function () {
            $("div#save .demo").bracket({
                teamWidth: 150,
                matchMargin: 20,
                roundMargin: 30,
                centerConnectors: true,
                disableHighlight: true,
                skipConsolationRound: true,
                init: saveData,
                save: userId ? save('left') : undefined,
                decorator: {
                    edit: edit_fn,
                    render: render_fn
                },
            });
        });
        $(function () {
            $("div#save1 .demo").bracket({
                dir: "rl",
                teamWidth: 150,
                matchMargin: 20,
                roundMargin: 30,
                centerConnectors: true,
                disableHighlight: true,
                skipConsolationRound: true,
                init: saveData1,
                save: userId ? save('right') : undefined,
                decorator: {
                    edit: edit_fn,
                    render: render_fn
                },
            });
        });
        $(function () {
            $("div#final .demo").bracket({
                dir: "rl",
                teamWidth: 150,
                matchMargin: 10,
                roundMargin: 0,
                centerConnectors: true,
                disableHighlight: true,
                init: finalData,
                save: userId ? save('final') : undefined,
                decorator: {
                    edit: edit_fn,
                    render: render_fn
                },
            });
        });
    </script>
    <script>
        var angleScale = {
            angle: 0,
            scale: 1,
        };
        var gestureArea = document.getElementById("gesture-area");
        var scaleElement = document.getElementById("scale-element");
        var resetTimeout;

        interact(gestureArea)
            .gesturable({
                listeners: {
                    start(event) {
                        angleScale.angle -= event.angle;

                        clearTimeout(resetTimeout);
                        scaleElement.classList.remove("reset");
                    },
                    move(event) {
                        // document.body.appendChild(new Text(event.scale))
                        var currentAngle = event.angle + angleScale.angle;
                        var currentScale = event.scale * angleScale.scale;

                        scaleElement.style.transform =
                            "rotate(" +
                            currentAngle +
                            "deg)" +
                            "scale(" +
                            currentScale +
                            ")";

                        // uses the dragMoveListener from the draggable demo above
                        dragMoveListener(event);
                    },
                    end(event) {
                        angleScale.angle = angleScale.angle + event.angle;
                        angleScale.scale = angleScale.scale * event.scale;

                        resetTimeout = setTimeout(reset, 1000);
                        scaleElement.classList.add("reset");
                    },
                },
            })
            .draggable({
                listeners: { move: dragMoveListener },
            });
        function dragMoveListener(event) {
            var target = event.target;
            // keep the dragged position in the data-x/data-y attributes
            var x = (parseFloat(target.getAttribute("data-x")) || 0) + event.dx;
            var y = (parseFloat(target.getAttribute("data-y")) || 0) + event.dy;

            // translate the element
            target.style.transform = "translate(" + x + "px, " + y + "px)";

            // update the posiion attributes
            target.setAttribute("data-x", x);
            target.setAttribute("data-y", y);
        }
        function reset() {
            scaleElement.style.transform = "scale(1)";

            angleScale.angle = 0;
            angleScale.scale = 1;
        }
    </script>
@endsection
