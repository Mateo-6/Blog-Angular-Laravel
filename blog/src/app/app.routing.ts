import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { ErrorComponent } from './components/error/error.component';
import { HomeComponent } from './components/home/home.component';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { CategoryNewComponent } from './components/category-new/category-new.component';
import { PostNewComponent } from './components/post-new/post-new.component';
import { PostDetailComponent } from './components/post-detail/post-detail.component';
import { PostEditComponent } from './components/post-edit/post-edit.component';
import { CategoryDetailComponent } from './components/category-detail/category-detail.component';
import { IdentityGuard } from './services/identity.guard';
import { ProfileComponent } from './components/profile/profile.component';

const routes: Routes = [

  { path: '', component: HomeComponent },
  { path: 'home', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'logout/:sure', component: LoginComponent},
  { path: 'register', component: RegisterComponent },
  { path: 'settings', component: UserEditComponent, canActivate: [IdentityGuard] },
  { path: 'new-category', component: CategoryNewComponent, canActivate: [IdentityGuard] },
  { path: 'new-post', component: PostNewComponent, canActivate: [IdentityGuard] },
  { path: 'post/:id', component: PostDetailComponent },
  { path: 'edit-post/:id', component: PostEditComponent },
  { path: 'category/:id', component: CategoryDetailComponent },
  { path: 'profile/:id', component: ProfileComponent },
  { path: '**', component: ErrorComponent },

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}
