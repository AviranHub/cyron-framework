<div :class="{'block': open, 'hidden': !open}" class="w-3/4 sm:w-3/4 lg:w-2/6 xl:w-1/4 bg-white h-screen shadow-md fixed right-0 md:fixed sm:fixed lg:static lg:block z-50">
    <div class="p-4">
        <img src="@asset('img/logo.png')" alt="Logo" class="h-8 w-8 mx-auto">
    </div>
    <nav class="mt-10" x-data="{ openDropdown: null }">
        <a href="@route('admin.dashboard',[])" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 text-right <?= isActive('/admin') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
            <i class="fas fa-tachometer-alt ml-4 text-indigo-500"></i>
            داشبورد
        </a>
        <a href="@route('admin.register')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 text-right <?= isActive('/admin/register') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
            <i class="fas fa-plus ml-4 text-indigo-500"></i>
            ثبت صنف
        </a>


        <!-- Dropdown 1 -->
        <div>
            <button @click="openDropdown === 1 ? openDropdown = null : openDropdown = 1" :class="openDropdown === 1 ? 'text-indigo-700' : ''" class="w-full text-right block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">
                <i class="fas fa-cog ml-4"></i>
                مشاهده اصناف
                <i :class="{'fa-chevron-down': openDropdown !== 1, 'fa-chevron-up': openDropdown === 1}" class="fas float-left"></i>
            </button>
            <div class="pr-8" x-show="openDropdown === 1">
                <a href="@route('admin.guilds',[])" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right <?= isActive('/admin/view-guilds') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
                    <i class="fas fa-list ml-4 text-indigo-500"></i>
                    همه اصناف
                </a>
                <a href="@route('admin.guilds.suggestion')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right <?= isActive('/admin/view-guilds') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
                    <i class="fas fa-star ml-4 text-yellow-600"></i>
                    اصناف پیشنهادی
                </a>
                <a href="@route('admin.guilds.denyes')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right <?= isActive('/admin/view-guilds') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
                    <i class="fas fa-times-circle ml-4 text-red-500"></i>
                    اصناف رد شده
                </a>
                <a href="@route('admin.guilds.waiting')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right <?= isActive('/admin/view-guilds') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
                    <i class="fas fa-circle-exclamation ml-4 text-yellow-400"></i>
                    اصناف در انتظار
                </a>
                <a href="@route('admin.guilds.confirms')" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right <?= isActive('/admin/view-guilds') ? 'bg-indigo-100 text-gray-900 border-r-4 border-indigo-500' : '' ?>">
                    <i class="fas fa-check-circle ml-4 text-green-500"></i>
                    اصناف تایید شده
                </a>
            </div>
        </div>

        <!-- Dropdown 2 -->
        <div>
            <button @click="openDropdown === 2 ? openDropdown = null : openDropdown = 2" :class="openDropdown === 2 ? 'text-indigo-700' : ''" class="w-full text-right block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">
                <i class="fas fa-tags ml-4"></i>
                دسته بندی اصناف
                <i :class="{'fa-chevron-down': openDropdown !== 2, 'fa-chevron-up': openDropdown === 2}" class="fas float-left"></i>
            </button>
            <div class="pr-8" x-show="openDropdown === 2">
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.add',[])">
                    <i class="fas fa-plus-circle ml-4 text-green-500"></i>
                    افزودن دسته
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.edit',[])">
                    <i class="fas fa-pen-to-square ml-4 text-orange-500"></i>
                    ویرایش دسته
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.delete',[])">
                    <i class="fas fa-minus-circle ml-4 text-red-500"></i>
                    حذف دسته
                </a>
            </div>
        </div>
        <!-- Dropdown 3 -->

        <div>
            <button @click="openDropdown === 3 ? openDropdown = null : openDropdown = 3" :class="openDropdown === 3 ? 'text-indigo-700' : ''" class="w-full text-right block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">
                <i class="fas fa-cogs ml-4"></i>
                تنظیمات عمومی
                <i :class="{'fa-chevron-down': openDropdown !== 3, 'fa-chevron-up': openDropdown === 3}" class="fas float-left"></i>
            </button>
            <div class="pr-8" x-show="openDropdown === 3">
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.setting.slider')">
                    <i class="fas fa-images ml-4 text-green-500"></i>
                    مدیریت اسلایدر
                </a>
            </div>
        </div>
        <!-- Dropdown 4 -->

        <div>
            <button @click="openDropdown === 4 ? openDropdown = null : openDropdown = 4" :class="openDropdown === 4 ? 'text-indigo-700' : ''" class="w-full text-right block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">
                <i class="fas fa-lock ml-4 text-green-700"></i>
                تنظیمات امنیتی
                <i :class="{'fa-chevron-down': openDropdown !== 4, 'fa-chevron-up': openDropdown === 4}" class="fas float-left"></i>
            </button>
            <div class="pr-8" x-show="openDropdown === 4">
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.add',[])">
                    <i class="fas fa-plus-circle ml-4 text-green-500"></i>
                    افزودن دسته
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.edit',[])">
                    <i class="fas fa-pen-to-square ml-4 text-orange-500"></i>
                    ویرایش دسته
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-100 text-right" href="@route('admin.guild.category.delete',[])">
                    <i class="fas fa-minus-circle ml-4 text-red-500"></i>
                    حذف دسته
                </a>
            </div>
        </div>

        
        
    </nav>
</div>

