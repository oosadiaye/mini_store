@extends('admin.layout')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">AI Design Assistant ✨</h2>
    <p class="text-gray-600">Let AI help you design your storefront and write compelling content.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Smart Design Assistant -->
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg border border-indigo-400 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 opacity-10 transform translate-x-4 -translate-y-4">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/></svg>
            </div>
            
            <h3 class="font-bold text-white mb-2 flex items-center text-xl">
                <span class="text-2xl mr-2">🤖</span> Store Designer
            </h3>
            <p class="text-indigo-100 text-sm mb-6">Describe your diverse store idea, and AI will build the theme, color palette, and layout structure instantly.</p>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-indigo-100 uppercase tracking-wider mb-2">Your Prompt</label>
                <textarea id="ai-prompt" rows="3" class="w-full rounded-lg border-0 shadow-inner bg-white/10 text-white placeholder-indigo-200 focus:ring-2 focus:ring-white focus:outline-none p-3 text-sm" placeholder="e.g. A dark futuristic gaming store with neon green accents..."></textarea>
            </div>

            <button id="btn-gen-design" class="w-full bg-white text-indigo-600 font-bold px-4 py-3 rounded-lg hover:bg-indigo-50 transition shadow-lg flex items-center justify-center">
                <span class="mr-2">✨</span> Generate My Store
            </button>
        </div>
    </div>

    <!-- Content Writing -->
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <span class="text-2xl mr-2">✍️</span> Copy Assistant
            </h3>
            <p class="text-gray-500 text-sm mb-4">Generate engaging text for your storefront.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Content Type</label>
                <select id="copy-type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="hero">Hero Headline</option>
                    <option value="about">About Us Description</option>
                </select>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-4 min-h-[100px] border border-gray-200 text-gray-700 text-sm italic" id="copy-result">
                Select a type and click generate...
            </div>

            <button id="btn-gen-copy" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <span class="mr-2">📝</span> Generate Copy
            </button>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-gen-design').addEventListener('click', function() {
        const btn = this;
        const prompt = document.getElementById('ai-prompt').value;
        
        if(!prompt.trim()) {
            alert('Please describe your store intuition!');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Designing...';
        
        // Simulating AI "Thinking"
        setTimeout(() => {
            fetch('{{ route('admin.ai.generate') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ prompt: prompt })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    if(confirm('Theme applied! View storefront now?')) {
                        window.open(data.redirect, '_blank');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong with the AI service.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<span class="mr-2">✨</span> Generate My Store';
            });
        }, 1500); // 1.5s delay to make it feel like "AI Work"
    });

    document.getElementById('btn-gen-copy').addEventListener('click', function() {
        const btn = this;
        const type = document.getElementById('copy-type').value;
        btn.disabled = true;
        btn.innerHTML = 'Writing...';
        
        fetch('{{ route('admin.ai.copy') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('copy-result').innerText = data.text;
                document.getElementById('copy-result').classList.remove('italic', 'text-gray-400');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="mr-2">📝</span> Generate Copy';
        });
    });
</script>
@endsection
