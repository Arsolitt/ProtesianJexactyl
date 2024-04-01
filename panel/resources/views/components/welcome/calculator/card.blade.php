@props(['title', 'description', 'memory', 'disk', 'egg', 'price'])



<div class="card">
    <h4>{{$title}}</h4>
    <span>{{$price}}</span>
    <p><span>{{$memory}}</span></p>
    <p><span>{{$disk}}</span></p>
    <p><span>{{$egg}}</span></p>
    <p>{{$description}}</p>
</div>

<script>
    console.log(document.querySelector('.card'))
</script>
