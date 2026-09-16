@php

    /*
    |--------------------------------------------------------------------------
    | Unique Form ID
    |--------------------------------------------------------------------------
    */

    $formKey =
        md5(
            (
                $row['sheet']
                ??
                ''
            )
            .
            '-'
            .
            (
                $row['row_number']
                ??
                ''
            )
            .
            '-'
            .
            (
                $row['student_name']
                ??
                ''
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Profile Candidates
    |--------------------------------------------------------------------------
    */

    $candidateCollection =
        isset(
            $profileCandidates
        )
            ? collect(
                $profileCandidates
            )
            : collect();


    $validCandidates =
        $candidateCollection
            ->filter(
                function ($candidate) {

                    return
                        !empty(
                            $candidate[
                                'external_id'
                            ]
                            ??
                            null
                        );
                }
            )
            ->values();


    /*
    |--------------------------------------------------------------------------
    | Normalise Function
    |--------------------------------------------------------------------------
    */

    $normaliseName =
        function ($name) {

            $name =
                mb_strtolower(
                    trim(
                        (string) $name
                    )
                );


            $name =
                preg_replace(
                    '/[^a-z0-9]+/u',
                    ' ',
                    $name
                );


            $name =
                preg_replace(
                    '/\s+/',
                    ' ',
                    $name
                );


            return trim(
                $name
            );
        };


    /*
    |--------------------------------------------------------------------------
    | Timeslot Name
    |--------------------------------------------------------------------------
    */

    $timeslotName =
        $normaliseName(
            $row[
                'student_name'
            ]
            ??
            ''
        );


    /*
    |--------------------------------------------------------------------------
    | Timeslot Name Tokens
    |--------------------------------------------------------------------------
    */

    $timeslotTokens =
        array_values(
            array_filter(
                explode(
                    ' ',
                    $timeslotName
                )
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Calculate Suggested Matches
    |--------------------------------------------------------------------------
    */

    $suggestions =
        $validCandidates
            ->map(
                function ($candidate) use (
                    $timeslotName,
                    $timeslotTokens,
                    $normaliseName
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Candidate Name
                    |--------------------------------------------------------------------------
                    */

                    $candidateFirstName =
                        $candidate[
                            'first_name'
                        ]
                        ??
                        '';


                    $candidateLastName =
                        $candidate[
                            'last_name'
                        ]
                        ??
                        '';


                    $candidateFullName =
                        trim(
                            $candidateFirstName
                            .
                            ' '
                            .
                            $candidateLastName
                        );


                    $candidateNormalised =
                        $normaliseName(
                            $candidateFullName
                        );


                    $candidateTokens =
                        array_values(
                            array_filter(
                                explode(
                                    ' ',
                                    $candidateNormalised
                                )
                            )
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Token Matching
                    |--------------------------------------------------------------------------
                    */

                    $matchingTokens =
                        array_intersect(
                            $timeslotTokens,
                            $candidateTokens
                        );


                    $matchingTokenCount =
                        count(
                            $matchingTokens
                        );


                    $timeslotTokenCount =
                        max(
                            count(
                                $timeslotTokens
                            ),
                            1
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Token Coverage
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    |
                    | Khiem Nguyen
                    |
                    | Bao Hoang Khiem Nguyen
                    |
                    | 2 / 2 timeslot tokens match
                    | = 100% token coverage
                    |--------------------------------------------------------------------------
                    */

                    $tokenCoverage =
                        (
                            $matchingTokenCount
                            /
                            $timeslotTokenCount
                        )
                        *
                        100;


                    /*
                    |--------------------------------------------------------------------------
                    | Surname Match
                    |--------------------------------------------------------------------------
                    */

                    $timeslotSurname =
                        !empty(
                            $timeslotTokens
                        )
                            ? end(
                                $timeslotTokens
                            )
                            : '';


                    $candidateSurname =
                        !empty(
                            $candidateTokens
                        )
                            ? end(
                                $candidateTokens
                            )
                            : '';


                    $surnameMatch =
                        (
                            $timeslotSurname
                            !==
                            ''
                            &&
                            $candidateSurname
                            !==
                            ''
                            &&
                            $timeslotSurname
                            ===
                            $candidateSurname
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | First Name Match
                    |--------------------------------------------------------------------------
                    */

                    $timeslotFirst =
                        $timeslotTokens[0]
                        ??
                        '';


                    $candidateFirst =
                        $candidateTokens[0]
                        ??
                        '';


                    $firstMatch =
                        (
                            $timeslotFirst
                            !==
                            ''
                            &&
                            in_array(
                                $timeslotFirst,
                                $candidateTokens,
                                true
                            )
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | String Similarity
                    |--------------------------------------------------------------------------
                    */

                    similar_text(
                        $timeslotName,
                        $candidateNormalised,
                        $similarityPercent
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Levenshtein Similarity
                    |--------------------------------------------------------------------------
                    */

                    $maximumLength =
                        max(
                            strlen(
                                $timeslotName
                            ),
                            strlen(
                                $candidateNormalised
                            ),
                            1
                        );


                    $distance =
                        levenshtein(
                            $timeslotName,
                            $candidateNormalised
                        );


                    $levenshteinPercent =
                        max(
                            0,
                            100
                            -
                            (
                                (
                                    $distance
                                    /
                                    $maximumLength
                                )
                                *
                                100
                            )
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Final Match Score
                    |--------------------------------------------------------------------------
                    |
                    | Token matching is deliberately weighted highest.
                    |
                    | This handles cases such as:
                    |
                    | Khiem Nguyen
                    | Bao Hoang Khiem Nguyen
                    |--------------------------------------------------------------------------
                    */

                    $score =
                        (
                            $tokenCoverage
                            *
                            0.55
                        )
                        +
                        (
                            $similarityPercent
                            *
                            0.20
                        )
                        +
                        (
                            $levenshteinPercent
                            *
                            0.10
                        );


                    /*
                     * Strong surname bonus.
                     */
                    if (
                        $surnameMatch
                    ) {

                        $score +=
                            10;
                    }


                    /*
                     * Given-name token bonus.
                     */
                    if (
                        $firstMatch
                    ) {

                        $score +=
                            10;
                    }


                    /*
                     * Every timeslot token appears in profile.
                     */
                    if (
                        $matchingTokenCount
                        ===
                        count(
                            $timeslotTokens
                        )
                        &&
                        $matchingTokenCount
                        >
                        0
                    ) {

                        $score +=
                            15;
                    }


                    /*
                     * Exact name.
                     */
                    if (
                        $timeslotName
                        ===
                        $candidateNormalised
                    ) {

                        $score =
                            100;
                    }


                    /*
                     * Limit to 100.
                     */
                    $score =
                        min(
                            100,
                            round(
                                $score
                            )
                        );


                    return [

                        'candidate' =>
                            $candidate,

                        'full_name' =>
                            $candidateFullName,

                        'score' =>
                            $score,

                        'token_matches' =>
                            $matchingTokenCount,

                        'surname_match' =>
                            $surnameMatch,
                    ];
                }
            )
            ->filter(
                function ($result) {

                    /*
                     * Only display useful suggestions.
                     */
                    return
                        $result[
                            'score'
                        ]
                        >=
                        45;
                }
            )
            ->sortByDesc(
                'score'
            )
            ->take(
                5
            )
            ->values();

@endphp



<div
    class="mt-3
           rounded-xl
           border
           border-red-200
           bg-red-50
           p-4"
>

    {{-- =====================================================
        TITLE
    ====================================================== --}}

    <div class="mb-3">

        <p
            class="text-xs
                   font-bold
                   text-red-700"
        >
            Match this name to the correct student
        </p>


        <p
            class="mt-1
                   text-xs
                   text-red-500"
        >
            The name on the left comes from the
            <strong>
                Timeslot workbook
            </strong>.

            Suggested names and the searchable list come from the
            <strong>
                Student Profile Report
            </strong>.
        </p>

    </div>



    {{-- =====================================================
        TIMESLOT NAME
    ====================================================== --}}

    <div
        class="mb-4
               rounded-lg
               border
               border-red-100
               bg-white
               px-3
               py-3"
    >

        <p
            class="text-[11px]
                   font-bold
                   uppercase
                   tracking-wide
                   text-slate-400"
        >
            Timeslot Name
        </p>


        <p
            class="mt-1
                   text-sm
                   font-bold
                   text-slate-800"
        >
            {{
                $row[
                    'student_name'
                ]
                ??
                'Unknown Student'
            }}
        </p>


        @if (
            !empty(
                $row[
                    'raw_student'
                ]
            )
        )

            <p
                class="mt-1
                       text-xs
                       text-slate-400"
            >
                Original value:

                {{
                    $row[
                        'raw_student'
                    ]
                }}
            </p>

        @endif


        @if (
            !empty(
                $row[
                    'sheet'
                ]
            )
        )

            <p
                class="mt-1
                       text-xs
                       text-slate-400"
            >
                Sheet:

                {{
                    $row[
                        'sheet'
                    ]
                }}

                @if (
                    !empty(
                        $row[
                            'row_number'
                        ]
                    )
                )

                    · Row:

                    {{
                        $row[
                            'row_number'
                        ]
                    }}

                @endif
            </p>

        @endif

    </div>



    {{-- =====================================================
        NO PROFILE STUDENTS
    ====================================================== --}}

    @if (
        $validCandidates->isEmpty()
    )

        <div
            class="rounded-lg
                   border
                   border-amber-200
                   bg-amber-50
                   px-3
                   py-3"
        >

            <p
                class="text-xs
                       font-semibold
                       text-amber-700"
            >
                No student profiles are available for matching.
            </p>

        </div>


    @else


        {{-- =================================================
            SUGGESTED MATCHES
        ================================================== --}}

        @if (
            $suggestions->isNotEmpty()
        )

            <div
                class="mb-4
                       rounded-xl
                       border
                       border-blue-200
                       bg-blue-50
                       p-3"
            >

                <div
                    class="mb-2
                           flex
                           items-center
                           justify-between"
                >

                    <div>

                        <p
                            class="text-xs
                                   font-bold
                                   text-blue-800"
                        >
                            Suggested Matches
                        </p>


                        <p
                            class="mt-0.5
                                   text-[11px]
                                   text-blue-600"
                        >
                            Select only after checking the Student ID and DOB.
                        </p>

                    </div>

                </div>


                <div class="space-y-2">


                    @foreach (
                        $suggestions
                        as $suggestion
                    )

                        @php

                            $suggestedCandidate =
                                $suggestion[
                                    'candidate'
                                ];


                            $suggestedId =
                                $suggestedCandidate[
                                    'external_id'
                                ]
                                ??
                                '';


                            $suggestedDob =
                                $suggestedCandidate[
                                    'date_of_birth'
                                ]
                                ??
                                null;


                            $suggestedDobDisplay =
                                $suggestedDob
                                    ? \Carbon\Carbon::parse(
                                        $suggestedDob
                                    )->format(
                                        'd/m/Y'
                                    )
                                    : 'No DOB';

                        @endphp


                        <button
                            type="button"
                            data-student-id="{{
                                $suggestedId
                            }}"
                            onclick="
                                selectSuggestedStudent_{{ $formKey }}(
                                    this.dataset.studentId
                                );
                            "
                            class="flex
                                   w-full
                                   items-center
                                   justify-between
                                   gap-3
                                   rounded-lg
                                   border
                                   border-blue-200
                                   bg-white
                                   px-3
                                   py-2.5
                                   text-left
                                   transition
                                   hover:border-blue-400
                                   hover:bg-blue-50"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate
                                           text-sm
                                           font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $suggestion[
                                            'full_name'
                                        ]
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    ID:
                                    {{
                                        $suggestedId
                                    }}

                                    · DOB:
                                    {{
                                        $suggestedDobDisplay
                                    }}
                                </p>

                            </div>


                            <div
                                class="shrink-0
                                       text-right"
                            >

                                <span
                                    class="
                                        inline-flex
                                        rounded-full
                                        px-2.5
                                        py-1
                                        text-xs
                                        font-bold

                                        @if (
                                            $suggestion['score']
                                            >=
                                            85
                                        )

                                            bg-green-100
                                            text-green-700

                                        @elseif (
                                            $suggestion['score']
                                            >=
                                            65
                                        )

                                            bg-blue-100
                                            text-blue-700

                                        @else

                                            bg-amber-100
                                            text-amber-700

                                        @endif
                                    "
                                >
                                    {{
                                        $suggestion[
                                            'score'
                                        ]
                                    }}%
                                </span>

                            </div>

                        </button>

                    @endforeach

                </div>

            </div>

        @else

            <div
                class="mb-4
                       rounded-lg
                       border
                       border-amber-200
                       bg-amber-50
                       px-3
                       py-2"
            >

                <p
                    class="text-xs
                           text-amber-700"
                >
                    No strong name suggestion was found.
                    Use the search box below.
                </p>

            </div>

        @endif



        {{-- =================================================
            FORM
        ================================================== --}}

        <form
            method="POST"
            action="{{
                route(
                    'admin.student-import.alias.store'
                )
            }}"
            class="space-y-3"
        >

            @csrf


            <input
                type="hidden"
                name="source_name"
                value="{{
                    $row[
                        'student_name'
                    ]
                    ??
                    ''
                }}"
            >



            {{-- =============================================
                SEARCH
            ============================================== --}}

            <div>

                <label
                    for="student_search_{{ $formKey }}"
                    class="mb-1
                           block
                           text-xs
                           font-semibold
                           text-slate-700"
                >
                    Search Student Profile
                </label>


                <input
                    type="text"
                    id="student_search_{{ $formKey }}"
                    placeholder="Type student name, Student ID or DOB..."
                    autocomplete="off"
                    class="w-full
                           rounded-lg
                           border
                           border-slate-300
                           bg-white
                           px-3
                           py-2.5
                           text-sm
                           text-slate-700
                           shadow-sm
                           focus:border-blue-500
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200"
                >


                <p
                    class="mt-1
                           text-xs
                           text-slate-400"
                >
                    Search by part of the first name,
                    surname, Student ID, or DOB.
                </p>

            </div>



            {{-- =============================================
                SELECT STUDENT
            ============================================== --}}

            <div>

                <label
                    for="student_select_{{ $formKey }}"
                    class="mb-1
                           block
                           text-xs
                           font-semibold
                           text-slate-700"
                >
                    Correct Student
                </label>


                <select
                    id="student_select_{{ $formKey }}"
                    name="target_external_id"
                    required
                    class="w-full
                           rounded-lg
                           border
                           border-slate-300
                           bg-white
                           px-3
                           py-2.5
                           text-sm
                           text-slate-700
                           shadow-sm
                           focus:border-blue-500
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200"
                >

                    <option
                        value=""
                        selected
                    >
                        Select correct student...
                    </option>


                    @foreach (
                        $validCandidates
                        as $candidate
                    )

                        @php

                            $candidateExternalId =
                                $candidate[
                                    'external_id'
                                ]
                                ??
                                '';


                            $candidateFirstName =
                                $candidate[
                                    'first_name'
                                ]
                                ??
                                '';


                            $candidateLastName =
                                $candidate[
                                    'last_name'
                                ]
                                ??
                                '';


                            $candidateDob =
                                $candidate[
                                    'date_of_birth'
                                ]
                                ??
                                null;


                            $candidateFullName =
                                trim(
                                    $candidateFirstName
                                    .
                                    ' '
                                    .
                                    $candidateLastName
                                );


                            $dobDisplay =
                                $candidateDob
                                    ? \Carbon\Carbon::parse(
                                        $candidateDob
                                    )->format(
                                        'd/m/Y'
                                    )
                                    : '';


                            $searchText =
                                mb_strtolower(
                                    trim(
                                        $candidateFullName
                                        .
                                        ' '
                                        .
                                        $candidateExternalId
                                        .
                                        ' '
                                        .
                                        $dobDisplay
                                    )
                                );

                        @endphp


                        <option
                            value="{{
                                $candidateExternalId
                            }}"
                            data-search="{{
                                $searchText
                            }}"
                        >

                            {{
                                $candidateFullName
                            }}

                            — ID:
                            {{
                                $candidateExternalId
                            }}

                            @if (
                                $candidateDob
                            )

                                — DOB:
                                {{
                                    $dobDisplay
                                }}

                            @endif

                        </option>

                    @endforeach

                </select>


                <p
                    id="student_count_{{ $formKey }}"
                    class="mt-1
                           text-xs
                           text-slate-400"
                >
                    {{
                        $validCandidates->count()
                    }}
                    student profile(s) available.
                </p>

            </div>



            {{-- =============================================
                SAVE
            ============================================== --}}

            <div
                class="flex
                       items-center
                       gap-2"
            >

                <button
                    type="submit"
                    class="inline-flex
                           h-9
                           items-center
                           justify-center
                           rounded-lg
                           bg-blue-600
                           px-4
                           text-xs
                           font-semibold
                           text-white
                           shadow-sm
                           transition
                           hover:bg-blue-700
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-300"
                >
                    Save Match
                </button>

            </div>

        </form>



        {{-- =================================================
            JAVASCRIPT
        ================================================== --}}

        <script>

            /*
            |--------------------------------------------------------------------------
            | Select Suggested Student
            |--------------------------------------------------------------------------
            */

            function selectSuggestedStudent_{{ $formKey }}(
                studentId
            ) {

                const select =
                    document.getElementById(
                        'student_select_{{ $formKey }}'
                    );


                if (
                    !select
                ) {

                    return;
                }


                select.value =
                    studentId;


                /*
                 * Scroll to the select field.
                 */
                select.scrollIntoView({
                    behavior:
                        'smooth',

                    block:
                        'center'
                });


                /*
                 * Highlight selection briefly.
                 */
                select.classList.add(
                    'ring-2',
                    'ring-green-300',
                    'border-green-400'
                );


                setTimeout(
                    function () {

                        select.classList.remove(
                            'ring-2',
                            'ring-green-300',
                            'border-green-400'
                        );

                    },
                    1500
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'DOMContentLoaded',
                function () {

                    const searchInput =
                        document.getElementById(
                            'student_search_{{ $formKey }}'
                        );


                    const select =
                        document.getElementById(
                            'student_select_{{ $formKey }}'
                        );


                    const countText =
                        document.getElementById(
                            'student_count_{{ $formKey }}'
                        );


                    if (
                        !searchInput
                        ||
                        !select
                    ) {

                        return;
                    }


                    const originalOptions =
                        Array.from(
                            select.options
                        )
                            .slice(
                                1
                            )
                            .map(
                                function (option) {

                                    return {

                                        value:
                                            option.value,

                                        text:
                                            option.text,

                                        search:
                                            (
                                                option.dataset.search
                                                ||
                                                option.text
                                            )
                                                .toLowerCase(),
                                    };
                                }
                            );


                    searchInput.addEventListener(
                        'input',
                        function () {

                            const search =
                                this.value
                                    .trim()
                                    .toLowerCase();


                            select.innerHTML =
                                '';


                            const placeholder =
                                document.createElement(
                                    'option'
                                );


                            placeholder.value =
                                '';


                            placeholder.textContent =
                                search
                                    ?
                                    'Select from matching students...'
                                    :
                                    'Select correct student...';


                            placeholder.selected =
                                true;


                            select.appendChild(
                                placeholder
                            );


                            const filtered =
                                originalOptions.filter(
                                    function (student) {

                                        if (
                                            search
                                            ===
                                            ''
                                        ) {

                                            return true;
                                        }


                                        /*
                                         * Split search words.
                                         */
                                        const searchWords =
                                            search
                                                .split(
                                                    /\s+/
                                                )
                                                .filter(
                                                    Boolean
                                                );


                                        /*
                                         * Every typed word must
                                         * appear somewhere.
                                         */
                                        return searchWords.every(
                                            function (word) {

                                                return student.search.includes(
                                                    word
                                                );
                                            }
                                        );
                                    }
                                );


                            filtered.forEach(
                                function (student) {

                                    const option =
                                        document.createElement(
                                            'option'
                                        );


                                    option.value =
                                        student.value;


                                    option.textContent =
                                        student.text;


                                    select.appendChild(
                                        option
                                    );
                                }
                            );


                            if (
                                countText
                            ) {

                                if (
                                    search
                                ) {

                                    countText.textContent =
                                        filtered.length
                                        +
                                        ' matching profile(s) found.';

                                } else {

                                    countText.textContent =
                                        originalOptions.length
                                        +
                                        ' student profile(s) available.';
                                }
                            }


                            if (
                                search
                                &&
                                filtered.length
                                ===
                                1
                            ) {

                                select.value =
                                    filtered[0].value;
                            }
                        }
                    );
                }
            );

        </script>

    @endif

</div>
