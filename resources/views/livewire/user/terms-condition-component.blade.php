<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            {{-- <h1 class="text-2xl font-bold text-gray-900 px-6 py-4">User T & C</h1> --}}
        </div>

        <div class="px-6 py-4 space-y-6">
            <h2 class="text-xl font-semibold mb-4 text-center">Guest Terms and Conditions</h2>

            <div class="space-y-6">
                @foreach($terms as $term)
                    <div>
                        <h3 class="font-medium mb-2">{{ $term->title }}</h3>
                        <div class="pl-4">
                            @if(is_array($term->content))
                                @foreach($term->content as $key => $content)
                                    @if(is_array($content))
                                        <div class="mb-2">
                                            <p class="text-gray-700 font-medium">{{ $key }})</p>
                                            <div class="pl-4 space-y-2">
                                                @foreach($content as $subKey => $subContent)
                                                    <p class="text-gray-700">{{ $subKey }}) {{ $subContent }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-700">{{ $key }}) {{ $content }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-gray-700">{{ $term->content }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
