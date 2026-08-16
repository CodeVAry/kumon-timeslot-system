<div
    class="flex justify-end
           gap-3 border-t
           border-slate-200
           bg-slate-50
           px-6 py-4"
>

    <a
        href="{{ route(
            'admin.students.show',
            $student
        ) }}"
        class="rounded-xl
               border border-slate-300
               bg-white px-5 py-2.5
               text-sm font-semibold
               text-slate-700
               hover:bg-slate-100"
    >
        Cancel
    </a>


    <button
        type="submit"
        class="rounded-xl
               bg-blue-600
               px-6 py-2.5
               text-sm font-semibold
               text-white
               hover:bg-blue-700"
    >
        Save Changes
    </button>

</div>
