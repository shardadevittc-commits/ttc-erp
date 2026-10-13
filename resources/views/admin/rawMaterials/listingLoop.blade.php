{{-- @forelse ($listing->items() as $row) --}}
@php
$row = [];
@endphp
    <tr>
        <td><a href="#" class="fw-medium">#1</a></td>
        <td>16</td>
        <td>BROADWAY STEEL INDUSTRIES</td>
        <td>FLAT</td>
        <td>---</td>
        <td>51X7</td>
        <td>0.00 Mt.</td>
        <td>20MnCr5</td>
        <td>14-09-2026 11:13:14</td>
        <td>10.210</td>
        <td>0.000</td>
        <td>10.210</td>
    </tr>
{{-- @empty
    <tr>
        <td colspan="7" class="text-center py-3">No sale orders found.</td>
    </tr>
@endforelse --}}
