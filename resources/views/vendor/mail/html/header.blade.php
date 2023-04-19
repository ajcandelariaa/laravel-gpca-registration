@props(['url'])
<tr>
<td class="header">
<a href="https://www.gpca.org.ae/" style="display: inline-block;">
@if (trim($slot) === 'GPCA Registration')
<img src="https://www.gpca.org.ae/wp-content/uploads/2017/10/80.png" class="logo" alt="GPCA Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
