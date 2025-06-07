<x-app-layout>
    <div class="main_container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <header class="priority_header">
                <form id="file5366Upload" action="{{ route('priority.upload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <label class="text-white" for="tablesImport">Choose xlsx file to upload</label>
                    <input class="form-input form-file-input" type="file" name="file5366" accept=".xlsx" id="file5366">
                    <button class="bg-white p-2" type="submit">Send</button>
                </form>
                @if (session('message'))
                    <p class="text-white">{{ session('message') }}</p>
                @endif
            </header>
        </div>
    </div>
</x-app-layout>