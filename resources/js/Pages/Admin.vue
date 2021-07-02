<template>
    <div>
        <app-layout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Admin
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                    <jet-validation-errors class="mb-4" />

                    <!-- category wrapper -->
                    <div class="bg-white rounded shadow px-4 py-4">
                        <div class="flex items-center text-sm mt-2">
                            <span
                                >Vul de gewenste categorie in en druk op enter
                                om deze toe te voegen</span
                            >
                        </div>
                        <!-- <input type="text" placeholder="titel" class=" rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full mt-4" v-model="categoryTitle" @keydown.enter="addCategory"> -->
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input
                                v-model="categoryTitle"
                                @keydown.enter="addCategory"
                                type="text"
                                class="
                                    focus:ring-indigo-500
                                    focus:border-indigo-500
                                    block
                                    w-full
                                    pl-2
                                    pr-12
                                    sm:text-sm
                                    border-gray-300
                                    rounded-md
                                "
                                placeholder="Hoe heet je categorie?"
                            />
                        </div>

                        <!-- category list -->
                        <ul class="category-list mt-4">
                            <li
                                v-for="category in admin_categories"
                                v-bind:key="category.id"
                                class="flex justify-between items-center mt-3"
                            >
                                <div class="flex items-center">
                                    <div v-if="category.id !== editingCategoryId" class="ml-3 text-sm font-semibold">
                                        {{ category.title }}
                                    </div>
                                    <div v-else class="ml-3 text-sm">
                                        <input
                                            v-model="editingCategoryTitle"
                                            @keydown.enter="updateCategory"
                                            type="text"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full p-0 border-gray-300 rounded-md"
                                            placeholder="Hoe heet je categorie?"
                                        /> <span>Druk op enter om op te slaan</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="p-2 bg-blue-300 text-blue-700 rounded-md text-sm">
                                        Admin {{ category.user.name }}
                                    </span>
                                    <button @click="editCategory(category.id)">
                                        <svg v-if="category.id !== editingCategoryId" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button @click="deleteCategory(category.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>


                </div>
            </div>
        </app-layout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
    components: {
        AppLayout,
        Welcome,
        JetValidationErrors,
    },

    props: {
        admin_categories: Object,
    },


    data() {
        return {
            categoryTitle: "",
            editingCategoryId: 0,
            editingCategoryTitle: null
        };
    },

    methods: {
        addCategory() {
            this.$inertia.post("/category", {
                title: this.categoryTitle,
                on_admin: true
            });
            this.categoryTitle = "";
        },

        deleteCategory(id) {
            this.$inertia.post("/category/" + id, {
                on_admin: true,
                _method: 'DELETE'
            });
        },

        editCategory(id) {
            if (this.editingCategoryId === id) {
                this.editingCategoryId = 0;
                this.editingCategoryTitle = null;
            } else {
                this.editingCategoryId = id;
                this.editingCategoryTitle = this.admin_categories[id].title;
            }
        },

        updateCategory() {
            this.$inertia.post(route('category.update', this.editingCategoryId), {
                title: this.editingCategoryTitle,
                on_admin: true,
                _method: 'PUT'
            });
            this.editingCategoryId = 0;
            this.editingCategoryTitle = null;
        },
    },
};
</script>
