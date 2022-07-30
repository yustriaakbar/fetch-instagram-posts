<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Instagram Feeds</title>
</head>
<body>
<!-- title -->
<div class="text-center p-10">
    <h1 class="font-bold text-4xl mb-4">Result Get Data From Instagram Posts</h1>
  </div>
  
  <!-- ✅ Grid Section - Starts Here 👇 -->
  <section id="Projects" class="w-fit mx-auto grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 justify-items-center justify-center gap-y-20 gap-x-14 mt-10 mb-5">
    
    @foreach ($instagram_posts as $post)
    <div class="w-72 bg-white shadow-md rounded-xl duration-500 hover:scale-105 hover:shadow-xl">
      <a href="#">
        <img src={{ $post->path }} alt="Product" class="h-80 w-72 object-cover rounded-t-xl" />
        <div class="px-4 py-3 w-72">
          <span class="text-gray-400 mr-3 text-xs">{{  $post->caption }}</span>
        </div>
      </a>
    </div>
    @endforeach

  </section>
  
  <!-- 🛑 Grid Section - Ends Here -->

</body>
</html>