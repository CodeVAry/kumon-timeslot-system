@extends('layouts.admin')

@section('title', 'Guardian Details')

@section('page-title', 'Guardian Details')


@php

    $savedNewGuardians =
        old(
            'new_guardians',
            $guardianData['new'] ?? []
        );


    if (
        empty(
            $savedNewGuardians
        )
    ) {

        $savedNewGuardians = [
            [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
            ],
        ];
    }

@endphp


@section('content')

<div class="mx-auto max-w-5xl">


    {{-- Registration Steps --}}
    <div
        class="mb-6
               flex
               flex-wrap
               items-center
               gap-3"
    >

        <a
            href="{{ route(
                'admin.student-registration.student'
            ) }}"
            class="font-semibold
                   text-blue-600"
        >
            ✓ Student Details
        </a>


        <span class="text-gray-400">
            →
        </span>


        <span
            class="font-semibold
                   text-blue-700"
        >
            2. Guardian Details
        </span>


        <span class="text-gray-400">
            →
        </span>


        <span class="text-gray-500">
            3. Class Enrolment
        </span>

    </div>



    {{-- Validation Errors --}}
    @if ($errors->any())

        <div
            class="mb-5
                   rounded-lg
                   border
                   border-red-200
                   bg-red-50
                   px-4 py-3
                   text-red-700"
        >

            <p class="font-semibold">
                Please correct the following:
            </p>


            <ul
                class="mt-2
                       list-inside
                       list-disc
                       text-sm"
            >

                @foreach (
                    $errors->all()
                    as $error
                )

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    <form
        method="POST"
        action="{{ route(
            'admin.student-registration.guardians.store'
        ) }}"
    >

        @csrf


        <div
            class="rounded-xl
                   border
                   border-gray-200
                   bg-white
                   shadow-sm"
        >


            {{-- Header --}}
            <div
                class="flex
                       items-center
                       justify-between
                       border-b
                       border-gray-200
                       p-6"
            >

                <div>

                    <h2
                        class="text-xl
                               font-bold
                               text-gray-800"
                    >
                        Guardian Details
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        Enter the guardian contact details.
                        Use the + button to add another guardian.
                    </p>

                </div>


                <button
                    type="button"
                    id="add-guardian"
                    title="Add another guardian"
                    class="flex
                           h-10 w-10
                           items-center
                           justify-center
                           rounded-full
                           bg-blue-600
                           text-2xl
                           font-bold
                           text-white
                           hover:bg-blue-700"
                >
                    +
                </button>

            </div>



            {{-- Dynamic Guardian Rows --}}
            <div
                id="new-guardian-container"
                class="space-y-5
                       p-6"
            ></div>



            {{-- Footer --}}
            <div
                class="flex
                       flex-col
                       gap-3
                       border-t
                       border-gray-200
                       bg-gray-50
                       p-6
                       sm:flex-row
                       sm:justify-end"
            >

                <a
                    href="{{ route(
                        'admin.student-registration.student'
                    ) }}"
                    class="rounded-lg
                           border
                           border-gray-300
                           bg-white
                           px-4 py-2.5
                           text-center
                           text-sm
                           font-semibold
                           text-gray-700
                           hover:bg-gray-100"
                >
                    ← Back
                </a>


                <button
                    type="submit"
                    class="rounded-lg
                           bg-blue-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Next: Class Enrolment →
                </button>

            </div>

        </div>

    </form>

</div>

@endsection



@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const container =
            document.getElementById(
                'new-guardian-container'
            );


        const addButton =
            document.getElementById(
                'add-guardian'
            );


        const savedGuardians =
            @json(
                array_values(
                    $savedNewGuardians
                )
            );


        function escapeHtml(
            value
        ) {

            return String(
                value ?? ''
            )
                .replaceAll(
                    '&',
                    '&amp;'
                )
                .replaceAll(
                    '<',
                    '&lt;'
                )
                .replaceAll(
                    '>',
                    '&gt;'
                )
                .replaceAll(
                    '"',
                    '&quot;'
                )
                .replaceAll(
                    "'",
                    '&#039;'
                );
        }


        function guardianTemplate(
            index,
            guardian = {}
        ) {

            return `

                <div
                    class="new-guardian-item
                           rounded-lg
                           border
                           border-gray-200
                           bg-gray-50
                           p-5"
                >

                    <div
                        class="mb-5
                               flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <h3
                                class="guardian-title
                                       font-semibold
                                       text-gray-800"
                            >
                                Guardian ${index + 1}
                            </h3>


                            <p
                                class="mt-1
                                       text-xs
                                       text-gray-500"
                            >
                                Enter guardian contact details.
                            </p>

                        </div>


                        <button
                            type="button"
                            class="remove-guardian
                                   rounded-lg
                                   bg-red-100
                                   px-3 py-1.5
                                   text-sm
                                   font-semibold
                                   text-red-700
                                   hover:bg-red-200"
                        >
                            Remove
                        </button>

                    </div>



                    <div
                        class="grid
                               gap-5
                               md:grid-cols-2"
                    >


                        <!-- First Name -->
                        <div>

                            <label
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-gray-700"
                            >
                                First Name

                                <span class="text-red-500">
                                    *
                                </span>
                            </label>


                            <input
                                type="text"
                                name="new_guardians[${index}][first_name]"
                                value="${escapeHtml(
                                    guardian.first_name
                                )}"
                                required
                                maxlength="100"
                                placeholder="First name"
                                class="w-full
                                       rounded-lg
                                       border-gray-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>



                        <!-- Last Name -->
                        <div>

                            <label
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-gray-700"
                            >
                                Last Name

                                <span class="text-red-500">
                                    *
                                </span>
                            </label>


                            <input
                                type="text"
                                name="new_guardians[${index}][last_name]"
                                value="${escapeHtml(
                                    guardian.last_name
                                )}"
                                required
                                maxlength="100"
                                placeholder="Last name"
                                class="w-full
                                       rounded-lg
                                       border-gray-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>



                        <!-- Email -->
                        <div>

                            <label
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-gray-700"
                            >
                                Email

                                <span class="text-red-500">
                                    *
                                </span>
                            </label>


                            <input
                                type="email"
                                name="new_guardians[${index}][email]"
                                value="${escapeHtml(
                                    guardian.email
                                )}"
                                required
                                maxlength="150"
                                placeholder="Email address"
                                class="w-full
                                       rounded-lg
                                       border-gray-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>



                        <!-- Phone -->
                        <div>

                            <label
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-gray-700"
                            >
                                Phone

                                <span class="text-red-500">
                                    *
                                </span>
                            </label>


                            <input
                                type="text"
                                name="new_guardians[${index}][phone]"
                                value="${escapeHtml(
                                    guardian.phone
                                )}"
                                required
                                maxlength="30"
                                placeholder="Phone number"
                                class="w-full
                                       rounded-lg
                                       border-gray-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>

                    </div>

                </div>

            `;
        }


        function addGuardian(
            guardian = {}
        ) {

            const index =
                container.querySelectorAll(
                    '.new-guardian-item'
                ).length;


            container.insertAdjacentHTML(
                'beforeend',
                guardianTemplate(
                    index,
                    guardian
                )
            );


            updateGuardianRows();
        }


        function updateGuardianRows()
        {
            const rows =
                container.querySelectorAll(
                    '.new-guardian-item'
                );


            rows.forEach(
                function (
                    row,
                    index
                ) {

                    const title =
                        row.querySelector(
                            '.guardian-title'
                        );


                    if (title) {

                        title.textContent =
                            'Guardian '
                            +
                            (
                                index
                                +
                                1
                            );
                    }


                    row.querySelectorAll(
                        'input'
                    )
                        .forEach(
                            function (
                                field
                            ) {

                                if (
                                    field.name
                                    &&
                                    field.name.startsWith(
                                        'new_guardians['
                                    )
                                ) {

                                    field.name =
                                        field.name.replace(
                                            /new_guardians\[\d+\]/,
                                            'new_guardians['
                                            +
                                            index
                                            +
                                            ']'
                                        );
                                }
                            }
                        );
                }
            );


            const removeButtons =
                container.querySelectorAll(
                    '.remove-guardian'
                );


            removeButtons.forEach(
                function (
                    button
                ) {

                    button.classList.toggle(
                        'hidden',
                        rows.length
                        ===
                        1
                    );
                }
            );
        }


        addButton.addEventListener(
            'click',
            function () {

                addGuardian();
            }
        );


        container.addEventListener(
            'click',
            function (
                event
            ) {

                if (
                    event.target.classList.contains(
                        'remove-guardian'
                    )
                ) {

                    const guardianItem =
                        event.target.closest(
                            '.new-guardian-item'
                        );


                    if (guardianItem) {

                        guardianItem.remove();
                    }


                    updateGuardianRows();
                }
            }
        );


        savedGuardians.forEach(
            function (
                guardian
            ) {

                addGuardian(
                    guardian
                );
            }
        );

    }
);

</script>

@endpush
