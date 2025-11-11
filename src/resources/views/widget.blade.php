<!-- resources/views/widget.blade.php -->
<div x-data="rateifyComponent({{ $item->id }}, '{{ addslashes(get_class($item)) }}', {{ $item->averageRating() }})" class="flex gap-1">
    <template x-for="i in 5">
        <svg @click="rate(i)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            :class="i <= rating ? 'fill-yellow-400' : 'fill-gray-300'"
            class="w-6 h-6 cursor-pointer transition-colors">
            <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.878 1.516 8.316L12 18.896l-7.452 4.604 1.516-8.316L0 9.306l8.332-1.151z"/>
        </svg>
    </template>
</div>

<script>
function rateifyComponent(itemId, modelClass, initialRating) {
    return {
        rating: initialRating,
        async rate(value) {
            try {
                const res = await fetch('{{ route('rateify.rate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        model: modelClass,
                        id: itemId,
                        value: value,
                    }),
                });
                const data = await res.json();
                if (res.ok) this.rating = data.average;
                else alert(data.message || 'حدث خطأ أثناء إرسال التقييم');
            } catch (err) {
                console.error(err);
                alert('فشل الاتصال بالخادم');
            }
        },
    };
}
</script>
