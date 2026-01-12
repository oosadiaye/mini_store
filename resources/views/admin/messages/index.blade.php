<x-admin-layout>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inbox') }}
            </h2>
        </div>
    </header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <message-inbox
                :initial-messages='@json($messages->items())'
                read-url-template="{{ route('admin.messages.read', ['tenant' => app('tenant')->slug, 'id' => ':id']) }}"
                csrf-token="{{ csrf_token() }}"
            >
                <template #pagination>
                    {{ $messages->links() }}
                </template>
            </message-inbox>
        </div>
    </div>
</x-admin-layout>
