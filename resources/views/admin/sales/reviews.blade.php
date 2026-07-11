@extends('layouts.dashboard')

@section('title', 'Reviews & Ratings - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Reviews & Ratings')

@section('content')
<div class="space-y-6">
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Reviews & Ratings</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage customer reviews and ratings on notes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="reviewSearch" placeholder="Search by customer, note, or comment..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="ratingFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
        </div>
    </div>

    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Reviews</h3>
            <span class="text-xs text-gray-500" id="reviewsCount">{{ $reviews->count() }} reviews</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="reviewsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Note</th>
                    <th class="px-6 py-3 font-medium">Rating</th>
                    <th class="px-6 py-3 font-medium">Comment</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($reviews as $index => $review)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $review->id }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($review->user->name ?? 'G', 0, 1)) }}</div>
                                <p class="text-sm font-medium text-gray-900">{{ $review->user->name ?? 'Guest' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-3"><span class="text-xs text-gray-700">{{ $review->note->title ?? 'Unknown' }}</span></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-3"><p class="text-xs text-gray-600 max-w-xs truncate">{{ $review->comment ?? 'No comment' }}</p></td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <button onclick="deleteReview({{ $review->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No reviews found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const reviewsTable = document.getElementById('reviewsTable').querySelector('tbody');
    const reviewSearch = document.getElementById('reviewSearch');
    const ratingFilter = document.getElementById('ratingFilter');
    const reviewsCount = document.getElementById('reviewsCount');
    let searchTimeout;

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function updateReviewsCount() {
        const total = reviewsTable.querySelectorAll('tr[data-id]').length;
        reviewsCount.textContent = total + ' review' + (total !== 1 ? 's' : '');
    }

    function deleteReview(id) {
        Swal.fire({
            title: 'Delete Review?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/sales/reviews') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast('success', result.message);
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => { row.remove(); updateReviewsCount(); }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => { showToast('error', 'Failed to delete review'); console.error(error); });
            }
        });
    }

    function loadReviews() {
        const term = reviewSearch.value.trim();
        const rating = ratingFilter.value;
        const url = new URL('{{ route('admin.sales.reviews') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (rating) url.searchParams.append('rating', rating);
        fetch(url, { method: 'GET', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(result => { if (result.success) renderReviewsTable(result.reviews); })
        .catch(error => { showToast('error', 'Error loading reviews'); console.error(error); });
    }

    function renderReviewsTable(reviews) {
        reviewsTable.innerHTML = '';
        if (reviews.length === 0) {
            reviewsTable.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No reviews found.</td></tr>';
            updateReviewsCount();
            return;
        }
        reviews.forEach((review, index) => {
            const user = review.user || { name: 'Guest' };
            const note = review.note || { title: 'Unknown' };
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += `<svg class="w-4 h-4 ${i <= review.rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200'}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
            }
            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', review.id);
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${(user.name || 'G').charAt(0).toUpperCase()}</div>
                        <p class="text-sm font-medium text-gray-900">${user.name || 'Guest'}</p>
                    </div>
                </td>
                <td class="px-6 py-3"><span class="text-xs text-gray-700">${note.title}</span></td>
                <td class="px-6 py-3"><div class="flex items-center gap-1">${stars}</div></td>
                <td class="px-6 py-3"><p class="text-xs text-gray-600 max-w-xs truncate">${review.comment || 'No comment'}</p></td>
                <td class="px-6 py-3 text-xs text-gray-500">${new Date(review.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-3 text-right">
                    <button onclick="deleteReview(${review.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            `;
            reviewsTable.appendChild(row);
        });
        updateReviewsCount();
    }

    reviewSearch.addEventListener('input', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(loadReviews, 400); });
    ratingFilter.addEventListener('change', loadReviews);
</script>
@endsection
