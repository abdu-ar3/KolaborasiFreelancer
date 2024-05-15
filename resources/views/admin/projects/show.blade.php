<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Details') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">
                
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="px-5 py-3 w-full rounded-3xl bg-red-500 text-white">
                            {{$error}}
                        </div>
                    @endforeach
                @endif

                <div class="item-card flex flex-row gap-y-10 justify-between md:items-center">
                    <div class="flex flex-row items-center gap-x-3">
                        <img src="#" alt="" class="rounded-2xl object-cover w-[120px] h-[90px]">
                        <div class="flex flex-col">
                            <h3 class="text-indigo-950 text-xl font-bold">lorem ipsum dolor</h3>
                            <p class="text-slate-500 text-sm">html</p>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-x-3">
                        <a href="#" class="font-bold py-4 px-6 bg-orange-500 text-white rounded-full">
                            Preview
                        </a>
                        <a href="#" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Add Tools
                        </a>
                    </div>

                    
                </div>

                <hr class="my-5">

                <h3 class="text-indigo-950 text-xl font-bold">Applicants</h3>


                <div class="flex flex-row justify-between items-center">
                    <div class="flex flex-row items-center gap-x-3">
                        <img src=" " alt="" class="rounded-full object-cover w-[70px] h-[70px]">
                        <div class="flex flex-col">
                            <h3 class="text-indigo-950 text-xl font-bold">saber jaya</h3>
                            <p class="text-slate-500 text-sm">programmer</p>
                        </div>
                    </div>


                    <span class="w-fit text-sm font-bold py-2 px-3 rounded-full bg-green-500 text-white">
                        HIRED
                    </span>


                    <span class="w-fit text-sm font-bold py-2 px-3 rounded-full bg-orange-500 text-white">
                        WAITING FOR APPROVAL
                    </span> 

                    <span class="w-fit text-sm font-bold py-2 px-3 rounded-full bg-red-500 text-white">
                        REJECTED
                    </span>


                    <div class="flex flex-row items-center gap-x-3">
                        <a href="#" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Details
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
